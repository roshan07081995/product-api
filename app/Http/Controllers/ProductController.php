<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Ratings;
use App\Interfaces\ProductRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    private ProductRepositoryInterface $productRepositoryInterface;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = ($request->query('perPage')) ? $request->query('perPage') : 10;

        $products = $this->productRepositoryInterface->index($perPage);

        $perPage = $products->perPage();
        $totalPages = ceil($products->total() / $perPage);

        $data =  [
            'results' => $products,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
        ];

        return ApiResponseClass::sendResponse($products,'',200);
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the validation rules as per your requirements
        ]);

        // Store the image
        $imageName = time().'.'.$request->image->extension();  
        $request->image->move(public_path('images'), $imageName);

        $details =[
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => 'images/'.$imageName,
        ];
        DB::beginTransaction();
        try{
      
            $product = $this->productRepositoryInterface->store($details);
            Ratings::create(['product_id' => $product->id , 'rate' => $request->rate,'count' => $request->count]);
            DB::commit();
            return ApiResponseClass::sendResponse(new ProductResource($product),'Product Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product = $this->productRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new ProductResource($product),'',200);
    
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $updateDetails =[
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'image' => $request->image,
        ];
        DB::beginTransaction();
        try{
             $product = $this->productRepositoryInterface->update($updateDetails,$product->id);

             DB::commit();
             return ApiResponseClass::sendResponse('Product Update Successful','',201);

        }catch(\Exception $ex){
            \Log::info($ex);
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->productRepositoryInterface->delete($product->id);

        return ApiResponseClass::sendResponse('Product Delete Successful','',204);
    
    }
}
