<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Product;



class ProductExport implements FromView,ShouldAutoSize
{	
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     public function __construct($id,$lang)
    {
    	
	         $this->id = $id;
           $this->lang = (string)$lang;
    	
    }

    public function view(): View
    {
      	$query = Product::where('vendor_id',$this->id)->get();


        foreach($query as $q){

              if($q->status == '1'){

                $q->status = trans('common.active');

              }else{

                $q->status = trans('common.inactive');
              }
              $lang             = $this->lang;
              $q->p_name        = $q->product_name;
              $q->created_at    = $q->created_at->format('y-m-d');
              
        }

      
        return view('admin.exports.products', [

            'data' => $query,
          ]);
    }
    
}
