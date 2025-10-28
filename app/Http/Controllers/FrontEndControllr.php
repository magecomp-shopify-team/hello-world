<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use app\Models\User;

class FrontEndControllr extends Controller
{

  public function index()
  {
    return view('welcome');
  }

  public function getdata(Request $request)
  {
    $session = Auth::user();
    $shop = $session->name;
    $response = $session->graph('
{
  products(first: 20) {
    nodes {
      id
      title
      handle
      variants(first: 5) {
        edges {
          node {
            id
            title
            price
          }
        }
      }
    }
  }
}');

    info($response);
    return response()->json($response);
  }
}



   


















//     public function getdata()
//     {
//         info("hello");
//         $storeName = config('services.shopify.store_name');
//         $accessToken = config('services.shopify.admin_access_token');

//         $graphqlEndpoint = "https://{$storeName}.myshopify.com/admin/api/2025-01/graphql.json";


       
//         $query = <<<GQL
//         {
//           products(first: 17 , query: "status:active" ) {
//             edges {
//               node {
//                 id
//                 title
//                 handle
                // variants(first: 1) {
                //   edges {
                //     node {
                //       id
                //       title
                //       price
                //       image {
                //         url
                //       }
                //     }
                //   }
                // }
//               }
//             }
//           }       
//         }
//         GQL;
        

//         try {
//             $response = Http::withHeaders([
//                 'X-Shopify-Access-Token' => $accessToken,
//                 'Content-Type' => 'application/json',
//             ])->post($graphqlEndpoint, [
//                 'query' => $query
//             ]);

//             if ($response->failed()) {
//                 return response()->json([
//                     'error' => 'Failed to fetch products',
//                     'details' => $response->body()
//                 ], 500);
//             }

//             $data = $response->json();

//             if (empty($data['data']['products']['edges'])) {
//                 return response()->json(['message' => 'No products found'], 404);
//             }

//             return response()->json($data['data']['products']['edges']);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

    
// }
