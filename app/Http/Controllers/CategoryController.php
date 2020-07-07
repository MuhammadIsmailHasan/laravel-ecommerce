<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $path = 'category';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::with(['parent'])
                        ->orderBy('created_at', 'DESC')
                        ->paginate(10);

        $parent = Category::getParent()->orderBy('name', 'ASC')->get();
        
        return view($this->path . '.index', compact('category', 'parent'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:categories'
        ]);
        
        // MENAMBAHKAN FIELD slug KE REQUEST
        $request->request->add(['slug' => $request->name]);

        Category::create($request->except('_token'));

        return redirect(route($this->path . '.index'))
                ->with(['success' => 'Berhasil menambahkan Category']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parent = Category::getParent()->orderBy('name', 'ASC')->get();

        return view($this->path . '.edit', compact('category', 'parent'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:categories,name,' .$id
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return redirect(route($this->path . '.index'))->with(['success', 'Kategori berhasil di perbarui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // ADAPUN withCount() SERUPA DENGAN EAGER LOADING YANG MENGGUNAKAN with()
        // HANYA SAJA withCount() RETURNNYA ADALAH INTEGER
        // JADI NNTI HASIL QUERYNYA AKAN MENAMBAHKAN FIELD BARU BERNAMA child_count YANG BERISI JUMLAH DATA ANAK KATEGORI
        $category = Category::withCount(['child', 'product'])->find($id);
        
        if ($category->child_count == 0 & $category->product_count == 0) {
            $category->delete();

            return redirect(route($this->path . '.index'))->with(['success' => 'Kategori Dihapus!']);
        }

        return redirect(route($this->path . '.index'))->with(['error' => 'Kategori ini memiliki anak kategori!']);
    }
}
