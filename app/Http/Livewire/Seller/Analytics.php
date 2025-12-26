<?php

namespace App\Http\Livewire\Seller;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;

class OrdersOverTime extends Component
{
    public $months = [];
    public $month;
    public $monthsBack = 12;
    public $summary = [];
    public $chartData = ['labels'=>[], 'data'=>[]];

    protected $queryString = ['month'];

    public function mount($month = null)
    {
        $this->generateMonthsList($this->monthsBack);
        $this->month = $month ?? now()->format('Y-m');
        $this->loadData();
    }

    public function generateMonthsList(int $back = 12)
    {
        $this->months = [];
        $now = now()->startOfMonth();

        for ($i = 0; $i < $back; $i++) {
            $date = $now->copy()->subMonths($i);
            $value = $date->format('Y-m');
            $label = $date->format('M Y');
            $this->months[$value] = $label;
        }
    }

    public function updatedMonth()
    {
        $this->loadData();
    }

    protected function loadData()
    {
        $storeId = auth()->user()->seller_store_id;

        [$year, $monthNum] = explode('-', $this->month);
        $start = now()->setDate($year, $monthNum, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $days = $start->daysInMonth;

        $rows = DB::table('orders as o')
            ->join('order_items as oi', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->where('p.seller_store_id', $storeId)
            ->whereBetween('o.order_date', [$start, $end])
            ->whereIn('o.status', ['paid','processing','completed','delivered'])
            ->selectRaw('EXTRACT(DAY FROM o.order_date)::integer as day, COUNT(DISTINCT o.id) as total')
            ->groupBy('day')
            ->pluck('total','day')
            ->toArray();


        $labels = [];
        $data = [];
        for ($d = 1; $d <= $days; $d++) {
            $labels[] = str_pad($d, 2, '0', STR_PAD_LEFT);
            $data[] = $rows[$d] ?? 0;
        }

        $this->chartData = ['labels'=>$labels, 'data'=>$data];
        $this->summary = ['total_orders' => array_sum($data)];

        $this->emit('ordersChartUpdated', $this->chartData);
    }

    public function render()
    {
        return view('livewire.seller.seller-analytics', [
            'months' => $this->months,
            'chartData' => $this->chartData,
            'month' => $this->month,
            'summary' => $this->summary
        ]);
    }
}
