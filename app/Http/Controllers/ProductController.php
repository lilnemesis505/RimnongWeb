<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Protype;
use Illuminate\Http\Request;
use ImageKit\ImageKit;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProductController extends Controller
{
    protected $imageKit;

    public function __construct()
    {
        $this->imageKit = new ImageKit(
            config('imagekit.public_key'),
            config('imagekit.private_key'),
            config('imagekit.url_endpoint')
        );
    }

    // แสดงรายการสินค้า
    public function index()
    {
        $types = Protype::all();
        $products = Product::paginate(10);
        return view('layouts.products.product', compact('products', 'types'));
    }

    // ฟอร์มเพิ่มสินค้า
    public function create()
    {
        $types = Protype::all();
        return view('layouts.products.add', compact('types'));
    }

    // บันทึกสินค้าใหม่
    public function store(Request $request)
    {
        $request->validate([
            'pro_name' => 'required|string|max:50',
            'price' => 'required|numeric',
            'type_id' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $data = $request->only('pro_name', 'price', 'type_id');
        $product = Product::create($data);

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $uploadResult = $this->imageKit->uploadFile([
                'file' => base64_encode(file_get_contents($image->getRealPath())),
                'fileName' => $image->getClientOriginalName(),
                'folder' => '/products'
            ]);


            // dd($uploadResult);


            if ($uploadResult->result->url !== null) {
                // บันทึก URL เต็มที่ได้จาก ImageKit ลงในฐานข้อมูล
                $product->image = $uploadResult->result->url;
                $product->image_id = $uploadResult->result->fileId;
                $product->save();
            } else {
                $errorMessage = is_object($uploadResult->error) && property_exists($uploadResult->error, 'message')
                    ? $uploadResult->error->message
                    : 'Unknown error';
                Log::error('ImageKit upload failed: ' . json_encode($uploadResult->error));
                return redirect()->back()->with('error', 'การอัปโหลดรูปภาพล้มเหลว: ' . $errorMessage);
            }
        }

        return redirect()->route('product.index')->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }
    public function filter(Request $request)
    {
        $typeId = $request->input('type_id');
        $query = Product::with('type');
        if ($typeId) {
            $query->where('type_id', $typeId);
        }
        $products = $query->paginate(12);
        $types = Protype::all();
        return view('layouts.products.product', compact('products', 'types'));
    }

    public function edit($pro_id)
    {
        $product = Product::with('type')->findOrFail($pro_id);
        $types = Protype::all();
        return view('layouts.products.edit', compact('product', 'types'));
    }

     public function update(Request $request, $pro_id)
    {
        // 1. Validate the request data
        $request->validate([
            'pro_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type_id' => 'required|exists:protype,type_id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $product = Product::findOrFail($pro_id);

        // 2. Handle image update if a new file is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // 2.1 Delete the old image from ImageKit if it exists
            if ($product->image_id) {
                try {
                    $this->imageKit->deleteFile($product->image_id);
                    Log::info('Deleted old image from ImageKit.', ['fileId' => $product->image_id]);
                } catch (\Exception $e) {
                    Log::error('Failed to delete old image from ImageKit.', ['error' => $e->getMessage()]);
                }
            }

            // 2.2 Upload the new image to ImageKit
            try {
                $uploadResult = $this->imageKit->uploadFile([
                    'file' => base64_encode(file_get_contents($image->getRealPath())),
                    'fileName' => uniqid() . '_' . $image->getClientOriginalName(),
                    'folder' => '/products'
                ]);

                // 2.3 Check for upload success
                if ($uploadResult->result->url !== null) {
                    $product->image = $uploadResult->result->url;
                    $product->image_id = $uploadResult->result->fileId;
                } else {
                    Log::error('ImageKit upload failed:', ['error' => $uploadResult->error]);
                    $errorMessage = property_exists($uploadResult->error, 'message') ? $uploadResult->error->message : 'Unknown error';
                    return redirect()->back()->with('error', 'การอัปโหลดรูปภาพใหม่ล้มเหลว: ' . $errorMessage);
                }
            } catch (\Exception $e) {
                Log::error('An unexpected error occurred during image upload: ' . $e->getMessage());
                return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ');
            }
        }

        // 3. Update other product data
        $product->pro_name = $request->pro_name;
        $product->price = $request->price;
        $product->type_id = $request->type_id;

        $product->save();

        // 4. Redirect with a success message
        return redirect()->route('product.index')->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }


    public function destroy($pro_id)
    {
        $product = Product::findOrFail($pro_id);
            if ($product->image_id) {
                try {
                    $this->imageKit->deleteFile($product->image_id);
                    Log::info('Deleted old image from ImageKit.', ['fileId' => $product->image_id]);
                } catch (\Exception $e) {
                    Log::error('Failed to delete old image from ImageKit.', ['error' => $e->getMessage()]);
                }
            }
        $product->delete();

        return redirect()->route('product.index')->with('success', 'ลบสินค้าสำเร็จแล้ว');
    }
   public function indexApi()
    {
        $today = Carbon::today();
        
        $products = Product::with(['promotions' => function ($query) use ($today) {
            $query->where('promo_start', '<=', $today)
                  ->where('promo_end', '>=', $today)
                  ->orderBy('promo_discount', 'desc')
                  ->limit(1);
        }])->get();

        $transformedProducts = $products->map(function ($product) {
            $activePromotion = $product->promotions->first();
            
            $specialPrice = null;
            $promoName = null;
            $promoId = null;
            $promoDiscount = null;

            if ($activePromotion) {
                $specialPrice = $product->price - $activePromotion->promo_discount;
                $promoName = $activePromotion->promo_name;
                // ✅ [FIX] Pass promo_id and promo_discount to the response
                $promoId = $activePromotion->promo_id;
                $promoDiscount = $activePromotion->promo_discount;
            }

            return [
                'pro_id' => $product->pro_id,
                'pro_name' => $product->pro_name,
                'price' => $product->price,
                'type_id' => $product->type_id,
                'image' => $product->image,
                'image_id' => $product->image_id,
                'special_price' => $specialPrice,
                'promo_name' => $promoName,
                // ✅ [FIX] Add new fields to the JSON response
                'promo_id' => $promoId,
                'promo_discount' => $promoDiscount,
            ];
        });

        return response()->json($transformedProducts);
    }
}

