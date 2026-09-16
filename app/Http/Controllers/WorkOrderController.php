<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkOrderRequest;
use App\Models\Asset;
use App\Models\SparePart;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkOrderController extends Controller
{
    public function index()
    {
        $workOrders = WorkOrder::with('asset', 'requester', 'technician')
            ->latest()
            ->paginate(10);

        $technicians = User::role('Teknisi')->orderBy('name')->get();

        return view('work_orders.index', compact('workOrders', 'technicians'));
    }

    public function create()
    {
        $assets = Asset::orderBy('name')->get();

        return view('work_orders.create', compact('assets'));
    }

    public function store(StoreWorkOrderRequest $request)
    {
        WorkOrder::create([
            'wo_code' => WorkOrder::generateWoCode(),
            'asset_id' => $request->asset_id,
            'requester_id' => auth()->id(),
            'status' => 'pending',
            'description' => $request->description,
        ]);

        return redirect()->route('work_orders.index')->with('success', 'Work Order berhasil diajukan.');
    }

    /**
     * Halaman detail work order: info lengkap + daftar spare part yang
     * sudah dipakai + form untuk menambah pemakaian part baru.
     */
    public function show(WorkOrder $work_order)
    {
        $work_order->load('asset', 'requester', 'technician', 'parts.sparePart');
        $spareParts = SparePart::orderBy('name')->get();
        $technicians = User::role('Teknisi')->orderBy('name')->get();

        return view('work_orders.show', [
            'workOrder' => $work_order,
            'spareParts' => $spareParts,
            'technicians' => $technicians,
        ]);
    }

    public function edit(WorkOrder $work_order)
    {
        $assets = Asset::orderBy('name')->get();

        return view('work_orders.edit', ['workOrder' => $work_order, 'assets' => $assets]);
    }

    public function update(StoreWorkOrderRequest $request, WorkOrder $work_order)
    {
        $work_order->update($request->validated());

        return redirect()->route('work_orders.index')->with('success', 'Work Order berhasil diperbarui.');
    }

    /**
     * Hapus work order. Kalau ada spare part yang sudah terlanjur
     * dipakai/dipotong stoknya, stok itu dikembalikan dulu supaya
     * data inventory tetap akurat.
     */
    public function destroy(WorkOrder $work_order)
    {
        DB::transaction(function () use ($work_order) {
            foreach ($work_order->parts as $usedPart) {
                $usedPart->sparePart->increment('stock', $usedPart->quantity);
            }
            $work_order->delete();
        });

        return redirect()->route('work_orders.index')->with('success', 'Work Order berhasil dihapus.');
    }

    /**
     * Manager/Supervisor menyetujui work order yang masih pending.
     */
    public function approve(WorkOrder $work_order)
    {
        $this->ensureStatus($work_order, 'pending', 'disetujui');

        $work_order->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', "Work Order {$work_order->wo_code} disetujui.");
    }

    /**
     * Manager/Supervisor menolak work order yang masih pending.
     */
    public function reject(Request $request, WorkOrder $work_order)
    {
        $this->ensureStatus($work_order, 'pending', 'ditolak');

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5'],
        ]);

        $work_order->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Work Order {$work_order->wo_code} ditolak.");
    }

    /**
     * Manager/Admin menugaskan teknisi tertentu untuk mengerjakan
     * work order. Bisa dilakukan setelah WO disetujui (approved) atau
     * saat sedang dikerjakan (in_progress), misalnya kalau teknisi
     * perlu diganti di tengah jalan.
     *
     * Kirim technician_id kosong untuk melepas penugasan.
     */
    public function assignTechnician(Request $request, WorkOrder $work_order)
    {
        if (! in_array($work_order->status, ['approved', 'in_progress'], true)) {
            throw ValidationException::withMessages([
                'technician_id' => "Teknisi hanya bisa ditugaskan saat Work Order berstatus 'approved' atau 'in_progress'. Status saat ini: '{$work_order->status}'.",
            ]);
        }

        $validated = $request->validate([
            'technician_id' => ['nullable', 'exists:users,id'],
        ]);

        // Kalau diisi, pastikan user yang dipilih memang punya role Teknisi,
        // supaya WO tidak ditugaskan ke Requester/Manager karena salah pilih.
        if (! empty($validated['technician_id'])) {
            $technician = User::findOrFail($validated['technician_id']);

            if (! $technician->hasRole('Teknisi')) {
                throw ValidationException::withMessages([
                    'technician_id' => "{$technician->name} bukan Teknisi, jadi tidak bisa ditugaskan ke Work Order.",
                ]);
            }

            $work_order->update(['technician_id' => $technician->id]);

            return back()->with('success', "Work Order {$work_order->wo_code} ditugaskan ke {$technician->name}.");
        }

        $work_order->update(['technician_id' => null]);

        return back()->with('success', "Penugasan teknisi pada Work Order {$work_order->wo_code} dilepas.");
    }

    /**
     * Teknisi mulai mengerjakan work order yang sudah disetujui.
     * Kalau WO sudah ditugaskan ke teknisi lain, teknisi yang login
     * tidak boleh mengambilnya. Kalau belum ditugaskan ke siapa pun,
     * teknisi yang login otomatis menjadi penanggung jawabnya.
     */
    public function start(WorkOrder $work_order)
    {
        $this->ensureStatus($work_order, 'approved', 'dimulai');

        if ($work_order->technician_id && $work_order->technician_id !== auth()->id()) {
            throw ValidationException::withMessages([
                'status' => "Work Order ini sudah ditugaskan ke {$work_order->technician->name}, jadi tidak bisa kamu kerjakan.",
            ]);
        }

        $work_order->update([
            'status' => 'in_progress',
            'technician_id' => $work_order->technician_id ?? auth()->id(),
            'started_at' => now(),
        ]);

        return back()->with('success', "Work Order {$work_order->wo_code} mulai dikerjakan.");
    }

    /**
     * Teknisi menyelesaikan work order yang sedang dikerjakan.
     */
    public function complete(WorkOrder $work_order)
    {
        $this->ensureStatus($work_order, 'in_progress', 'diselesaikan');

        $work_order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', "Work Order {$work_order->wo_code} selesai dikerjakan.");
    }

    /**
     * Menambahkan pemakaian spare part ke work order yang sedang
     * dikerjakan (in_progress). Stok spare part otomatis berkurang.
     * Kalau part yang sama sudah pernah ditambahkan sebelumnya,
     * kuantitasnya digabung (ditambah), bukan dibuat baris baru.
     */
    public function addPart(Request $request, WorkOrder $work_order)
    {
        if ($work_order->status !== 'in_progress') {
            throw ValidationException::withMessages([
                'status' => 'Spare part hanya bisa ditambahkan saat Work Order berstatus in_progress.',
            ]);
        }

        $validated = $request->validate([
            'spare_part_id' => ['required', 'exists:spare_parts,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($work_order, $validated) {
            $sparePart = SparePart::lockForUpdate()->findOrFail($validated['spare_part_id']);

            if ($sparePart->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok {$sparePart->name} tidak cukup. Sisa stok: {$sparePart->stock}.",
                ]);
            }

            $existing = WorkOrderPart::where('work_order_id', $work_order->id)
                ->where('spare_part_id', $sparePart->id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $validated['quantity']);
            } else {
                WorkOrderPart::create([
                    'work_order_id' => $work_order->id,
                    'spare_part_id' => $sparePart->id,
                    'quantity' => $validated['quantity'],
                ]);
            }

            $sparePart->decrement('stock', $validated['quantity']);
        });

        return back()->with('success', 'Spare part berhasil ditambahkan ke Work Order.');
    }

    /**
     * Membatalkan pemakaian spare part (stok dikembalikan). Hanya
     * boleh dilakukan selama work order masih in_progress, supaya
     * riwayat pemakaian pada WO yang sudah selesai tidak diubah-ubah.
     */
    public function removePart(WorkOrder $work_order, WorkOrderPart $work_order_part)
    {
        abort_unless($work_order_part->work_order_id === $work_order->id, 404);

        if ($work_order->status !== 'in_progress') {
            throw ValidationException::withMessages([
                'status' => 'Pemakaian spare part hanya bisa dibatalkan saat Work Order masih in_progress.',
            ]);
        }

        DB::transaction(function () use ($work_order_part) {
            $work_order_part->sparePart->increment('stock', $work_order_part->quantity);
            $work_order_part->delete();
        });

        return back()->with('success', 'Pemakaian spare part berhasil dibatalkan, stok dikembalikan.');
    }

    /**
     * Helper: pastikan work order sedang berada di status yang tepat
     * sebelum aksi dijalankan, supaya tidak ada lompatan status yang
     * tidak valid (misal: complete padahal masih pending).
     */
    protected function ensureStatus(WorkOrder $workOrder, string $expectedStatus, string $actionLabel): void
    {
        if ($workOrder->status !== $expectedStatus) {
            throw ValidationException::withMessages([
                'status' => "Work Order tidak bisa {$actionLabel} karena statusnya saat ini '{$workOrder->status}', bukan '{$expectedStatus}'.",
            ]);
        }
    }
}
