<?php

namespace App\Http\Livewire\Admin;

use App\Models\ServiceCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AdminServiceCategoryComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $name;
    public $slug;
    public $image;

    public function generateSlug()
    {
        $this->slug = Str::slug($this->name);
    }

    public function storeServiceCategory()
    {
        $this->validate([
            'name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Generate unique filename
        $imageName = time() . '.' . $this->image->getClientOriginalExtension();
        
        // Ensure the directory exists
        $destinationPath = public_path('images/categories');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Save the uploaded file
        $fullPath = $destinationPath . '/' . $imageName;
        
        // Use file handling to save the image
        File::put($fullPath, file_get_contents($this->image->getRealPath()));

        ServiceCategory::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $imageName
        ]);

        session()->flash('message', 'Service Category has been created successfully!');
        $this->resetInputFields();
    }

    public function deleteServiceCategory($id)
    {
        $category = ServiceCategory::findOrFail($id);
        
        // Delete the image file
        $imagePath = public_path('images/categories/' . $category->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // Delete the category
        $category->delete();

        session()->flash('message', 'Service Category has been deleted successfully!');
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->slug = '';
        $this->image = null;
    }

    public function render()
    {
        $scategories = ServiceCategory::paginate(10);
        return view('livewire.admin.admin-service-category-component',['scategories'=>$scategories])->layout('layouts.base');
    }
}
