<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BundleController extends Controller
{
    public function saveBundle(Request $request)
    {
        $session = Auth::user();
        $shop = $session->name;
        $shopData = User::where('name', $shop)->first();

        $mainProduct = $request->input('mainProduct');
        $bundleProducts = $request->input('bundleProducts');
        $bundlePrice = $request->input('bundlePrice');

        if (!$mainProduct || empty($bundleProducts)) {
            return response()->json([
                'success' => false,
                'message' => 'Main product or bundle products missing.'
            ]);
        }


        $bundle = Bundle::updateOrCreate(
            ['main_product_id->id' => $mainProduct['id']],
            [
                'main_product_id'   => json_encode($mainProduct),
                'main_product_title' => $mainProduct['title'],
                'bundle_product_ids' => $bundleProducts,
                'bundle_price'      => $bundlePrice ?? null,
                'session_id'        => $shopData->id,
            ]
        );


        $productIds = array_map(fn($p) => $p['id'], $bundleProducts);

        $mutation = '
        mutation metafieldsSet($metafields: [MetafieldsSetInput!]!) {
            metafieldsSet(metafields: $metafields) {
                metafields {
                    id
                    namespace
                    key
                    value
                    type
                }
                userErrors {
                    field
                    message
                }
            }
        }';

        $variables = [
            'metafields' => [
                [
                    'ownerId'  => $mainProduct['id'],
                    'namespace' => 'custom',
                    'key'      => 'tech',
                    'type'     => 'list.product_reference',
                    'value'    => json_encode($productIds),
                ]
            ]
        ];

        try {
            $response = $session->graph($mutation, $variables);
            $errors = $response['body']['data']['metafieldsSet']['userErrors'] ?? [];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shopify GraphQL error: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => empty($errors),
            'errors'  => $errors,
            'bundle'  => $bundle,
            'response' => $response['body']['data']['metafieldsSet'] ?? null,
        ]);
    }


    public function getBundles()
    {
        $bundles = Bundle::all()->map(function ($bundle) {
            return [
                'id' => $bundle->id,
                'main_product_id' => json_decode($bundle->main_product_id, true), 
                'main_product_title' => $bundle->main_product_title,
                'bundle_product_ids' => json_decode($bundle->bundle_product_ids, true), 
                'bundle_price' => $bundle->bundle_price,
            ];
        });

        return response()->json($bundles);
    }

      public function deleteBundle($id)
    {
        $user = Auth::user();
        $bundle = Bundle::where('id', $id)->where('session_id', $user->id)->first();
        $bundle->delete();
 
        return response()->json(['success' => true, 'message' => 'Bundle deleted successfully']);
    }
}
