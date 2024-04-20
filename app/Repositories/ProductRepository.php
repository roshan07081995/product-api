<?php

namespace App\Repositories;
use App\Models\Product;
use App\Interfaces\ProductRepositoryInterface;
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        
    }

    public function index($perPage){
        return Product::with(['category' => function ($query) {
            $query->select('id', 'name');
        }])->with(['rating' => function ($query) {
            $query->select('rate', 'count','product_id');
        }])->paginate($perPage);
    }

    public function getById($id){
       return Product::findOrFail($id);
    }

    public function store(array $data){
   
        return Product::create($data);
   
        
    }

    public function update(array $data,$id){
  
       return Product::whereId($id)->update($data);
     
    }
    
    public function delete($id){
       Product::destroy($id);
    }
}
