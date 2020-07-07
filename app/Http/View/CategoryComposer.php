<?php
namespace App\Http\View;

use App\Category;
use Illuminate\View\View;

class CategoryComposer 
{
    public function compose(View $view) 
    {   
        $categories = Category::with(['child'])
            ->withCount(['child'])
            ->getParent()
            ->orderBy('name', 'ASC')
            ->get();

        $view->with('categories', $categories);
    }
}