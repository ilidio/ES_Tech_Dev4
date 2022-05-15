<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    public static function import_from_csv() {
        // sku,account_ref,user_ref,quantity,value
        $file_handle = fopen("es_resources/import.csv", 'r');
        fgetcsv($file_handle, 0, ",");
        while (!feof($file_handle)) {
            $price = fgetcsv($file_handle, 0, ",");
            print("--------------\n");
            print("Price");
            print_r($price);
            if ($price) {
                $product = DB::table('products')->where('sku', $price[0])->first();
                $product_id = null;
                print_r($product);
                if ($product!=null) {
                    print("ProductID: $product->id\n");
                    $product_id = $product->id;
                    print("\n");
                } else {
                    $insert = DB::insert('insert into products (sku)
                    values (?)', [$price[0]]);
                    $lastId = $insert->id;
                }
                $user = DB::table('users')->where('external_reference', $price[2])->first();
                $account = DB::table('accounts')->where('external_reference', $price[1])->first();
                $user_id = null;
                if ($user!=null) {
                    print("User: $user->id\n");
                    $user_id = $user->id;
                }
                $account_id = null;
                if ($account!=null) {
                    print("AccountId: $account->id \n");
                    $account_id = $account->id;
                }

              // Check if product is already in database
              $price_exists = DB::table('prices')
                  ->where('product_id',$product_id)
                  ->where('account_id', $account_id)
                  ->where('user_id', $user_id)
                  ->where('quantity', $price[3])
                  ->where('value', $price[4])
                  ->first();
              if ($price_exists == null) {
                  DB::insert('insert into prices (product_id, account_id, user_id, quantity, value, created_at)
                  values (?, ?, ?, ?, ?, NOW())', [$product_id, $account_id, $user_id, $price[3], $price[4]]);
              } else {
                  print("This Price already exists\n");
              }
              print("--------------\n");
            }
        }
        fclose($file_handle);
        print("Import done!!");
    }

    public static function live_stream() {
        $jsonString = file_get_contents(base_path('es_resources/live_prices.json'));
        $data = json_decode($jsonString, true);
        return  response()->json($data);
    }

    public static function get_product_price(Request $request, $product_codes) {
        $account_id = $request->account_id ?? null;
        $skus_array = preg_split("/\,/", $product_codes);
        $results = [];
        foreach ($skus_array as $sku) {
            // Product exists?
            $product = DB::table('products')
                ->where('sku', $sku)
                ->first();
            if ($product == null) {
                continue;
            }

            // Json Live Prices
            $live_prices = file_get_contents(base_path('es_resources/live_prices.json'));
            $live_prices = json_decode($live_prices, true);
            $collection = collect($live_prices);
            if ($account_id !== null) {
                $filtered_price = $collection->whereIn('sku', $skus_array)
                    ->whereIn('account_id', [$account_id])->sortBy("price", SORT_REGULAR);
                $price = null;
                if ($filtered_price->all()) {
                    $price = array_slice($filtered_price->all(), 0, 1)[0];
                }
            }
            $filtered_public_price = $collection->whereIn('sku', $skus_array)
                ->whereNull('account')->sortBy("price", SORT_REGULAR);
            $public_price = null;
            if ($filtered_public_price->all()) {
                $public_price = array_slice($filtered_public_price->all(), 0, 1)[0];
            }
            $result = null;
            if ($account_id !== null && $price !== null) {
                $result = ["sku" => $sku, "price" =>  $price["price"]];
            } else if ($public_price !== null) {
                $result = ["sku" => $sku, "price" =>  $public_price["price"]];
            }
            if ($result != null) {
                $results[] = $result;
                continue;
            }

            // Database get price logic
            $result = DB::select('call Select_Lowest_Price(?,?)', array($account_id, $product->id))[0];
            if ($result != null) {
                $results[] = $result;
            }
        }

        return $results;
    }
}
