<?php

namespace App\Controllers;

use App\Models\Category;

class CategoryController {
    public function index() {
        try {
            $categories = Category::toArray();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
