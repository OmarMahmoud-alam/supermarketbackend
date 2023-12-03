<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\category;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel ,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       // $categorId=category::where('name',$row['category'])->first('id');
      // Log::info(implode(',', $row->key));
       Log::info( array_keys($row));
       /* if (!isset($row[0])) {
            return null;
        }*/
        return new Product([
            'image'=>$row['image'],
           
            'brand'=>$row['brand'],
            'name'=>$row['name'],
            'description'=>$row['description']?? null,
            'isvisible'=>$row['isvisible'],
            'availiability'=>$row['availiability'] ?? true,
            'price'=>$row['price'],
            'quantity'=>$row['quantity'],
            'type'=>$row['type'],
            'category_id'=>$row['category_id'],
           // 'category_id'=>$categorId,
            
        ]);
    }
}
