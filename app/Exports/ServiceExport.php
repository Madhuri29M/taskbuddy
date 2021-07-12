<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Service;


class ServiceExport implements FromView,ShouldAutoSize
{	
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     public function __construct($id)
    {
    	
	        $this->id = $id;
	       
    	
    }

    public function view(): View
    {
    	$query = Service::where('vendor_id',$this->id)->get();


    	foreach($query as $q){

            $q->created_at    = $q->created_at->format('y-m-d');
            $q->category_name = $q->category ? $q->category->category_name : '';
      }

      
      return view('admin.exports.services', [

          'data' => $query,
      ]);
    }
}
