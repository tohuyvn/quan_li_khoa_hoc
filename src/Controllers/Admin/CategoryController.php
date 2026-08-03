<?php

namespace App\Controllers\Admin;

use App\Models\Category;

class CategoryController
{
    private Category $categories;

    public function __construct()
    {
        $this->categories = new Category();
    }

    public function index(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
        view('admin.categories.index', ['categories' => $this->categories->all()]);
    }

    public function createForm(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
        view('admin.categories.form', ['category' => null]);
    }

    public function store(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            setOld($_POST);
            flash('error', 'Tên danh mục không được để trống.');
            redirect('/admin/categories/create');
        }
        $this->categories->create($_POST);
        flash('success', 'Thêm danh mục thành công.');
        redirect('/admin/categories');
    }

    public function editForm(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
        $category = $this->categories->find($id);
        if (!$category) {
            redirect('/admin/categories');
        }
        view('admin.categories.form', ['category' => $category]);
    }

    public function update(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            setOld($_POST);
            flash('error', 'Tên danh mục không được để trống.');
            redirect("/admin/categories/{$id}/edit");
        }
        $this->categories->update($id, $_POST);
        flash('success', 'Cập nhật danh mục thành công.');
        redirect('/admin/categories');
    }

    public function destroy(int $id): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
        
        $db = \App\DB::connection();
        try {
            // Check if there are any products under this category
            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmtCheck->execute([$id]);
            $productCount = (int)$stmtCheck->fetchColumn();
            
            if ($productCount > 0) {
                flash('error', 'Không thể xóa danh mục này vì vẫn còn sản phẩm thuộc danh mục. Vui lòng di chuyển hoặc xóa các sản phẩm thuộc danh mục này trước.');
            } else {
                $this->categories->delete($id);
                flash('success', 'Xóa danh mục thành công.');
            }
        } catch (\Throwable $e) {
            flash('error', 'Lỗi khi xóa danh mục: ' . $e->getMessage());
        }
        
        redirect('/admin/categories');
    }
}
