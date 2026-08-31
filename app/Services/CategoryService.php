<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Result;
use App\Models\Category;

class CategoryService
{
    public function __construct(
        protected Result $result,  
        protected Category $categoryModel
    ) {}

    public function all(): Result
    {
        $categories = $this->categoryModel->all();

        return $this->result->success('All categories fetched', ['categories' => $categories]);
    }

    public function group(): Result
    {
        $categories = $this->categoryModel->group();

        return $this->result->success('Categories with product counts fetched', ['categories' => $categories]);
    }

    public function create(
        string $category
    ): Result {

        $created = $this->categoryModel->create($category);
        if ($created === false) {
            return $this->result->error('Failed to create category', [], 500);
        }

        return $this->result->success('Category created successfully', [], 201);
    }

    public function update(
        string $name, 
        int $categoryId
    ): Result {

        $updated = $this->categoryModel->update($name, $categoryId);
        if ($updated === false) {
            return $this->result->error('Failed to update category', 500);
        }

        return $this->result->success('Category updated successfully');
    }

    public function delete(
        int $categoryId
    ): Result { 

        $deleted = $this->categoryModel->delete($categoryId);
        if ($deleted === false) {
            return $this->result->error('Failed to delete category', 500);
        }
        
        return $this->result->success('Category deleted successfully');
    }

    public function count(): Result
    { 
        $count = $this->categoryModel->count();

        return $this->result->success('Categories counted', ['count' => $count]);
    }
    
    public function fetch(
        string $category
    ): Result { 

        $subcategories = $this->categoryModel->fetch($category);
        
        return $this->result->success('Sub categories fetched', ['subcategories' => $subcategories]);
    }
}
