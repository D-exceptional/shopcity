<?php

namespace App\Services;

use App\Helpers\ResponseManager;
use App\Models\Category;

class CategoryController
{
    protected ResponseManager $response;
    protected Category $categoryModel;

    public function __construct(ResponseManager $response, Category $categoryModel)
    {
        $this->response = $response;
        // Required model for this controller class
        $this->categoryModel = $categoryModel;
    }

    public function all()
    {
        $categories = $this->categoryModel->all();

        // Empty cart is NOT an error
        return $this->response->success('All categories fetched', ['categories' => $categories]);
    }

    public function group()
    {
        $categories = $this->categoryModel->group();

        // Empty cart is NOT an error
        return $this->response->success('Categories with product counts fetched', ['categories' => $categories]);
    }

    public function create(string $category)
    {
        $created = $this->categoryModel->create($category);
        if ($created === false) {
            return $this->response->fail('Failed to create category', [], 500);
        }

        return $this->response->success('Category created successfully', [], 201);
    }

    public function update(string $name, int $id)
    {
        $updated = $this->categoryModel->update($name, $id);
        if ($updated === false) {
            return $this->response->fail('Failed to update category', 500);
        }

        return $this->response->success('Category updated successfully');
    }

    public function delete(int $id)
    { 
        $deleted = $this->categoryModel->delete($id);
        if ($deleted === false) {
            return $this->response->fail('Failed to delete category', 500);
        }
        
        return $this->response->success('Category deleted successfully');
    }

    public function count()
    { 
        $count = $this->categoryModel->count();
        return $this->response->success('Categories counted', ['count' => $count]);
    }
    
    public function fetch(string $category)
    { 
        $subcategories = $this->categoryModel->fetch($category);
        return $this->response->success('Sub categories fetched', ['subcategories' => $subcategories]);
    }
}
