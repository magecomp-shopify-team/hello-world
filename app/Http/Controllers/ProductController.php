<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Create new product
    public function createProduct(Request $request)
    {
        $session = Auth::user();
        $title = $request->input('title');
        $description = $request->input('description');

        $mutation = '
        mutation productCreate($input: ProductInput!) {
          productCreate(input: $input) {
            product {
              id
              title
              descriptionHtml
            }
            userErrors {
              field
              message
            }
          }
        }';

        $variables = [
            'input' => [
                
                'title' => $title,
                'descriptionHtml' => $description,
            ],
        ];

        $response = $session->graph($mutation, $variables);

        return response()->json($response);
    }

    // Update existing product
    public function update(Request $request)
{
    $session = Auth::user();
    $id = $request->input('id'); // GID from body
    $title = $request->input('title');
    $description = $request->input('description');

    $mutation = '
    mutation productUpdate($input: ProductInput!) {
      productUpdate(input: $input) {
        product {
          id
          title
          descriptionHtml
        }
        userErrors {
          field
          message
        }
      }
    }';

    $variables = [
        'input' => [
            'id' => $id,   
            'title' => $title,
            'descriptionHtml' => $description,
        ],
    ];

    $response = $session->graph($mutation, $variables);

    return response()->json($response);
}


    // Fetch products
    public function getProducts()
    {
        $session = Auth::user();

        $query = '
        {
          products(first: 50) {
            nodes {
              id
              title
              descriptionHtml
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
        }';

        $response = $session->graph($query);

        return response()->json($response);
    }
}
