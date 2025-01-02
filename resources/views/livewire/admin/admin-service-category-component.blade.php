<div>
    <style>
        nav svg{
            height: 20px
        }
        nav .hidden{
            display: block !important;
        }
    </style>
    <div class="section-title-01 honmob">
        <div class="bg_parallax image_02_parallax"></div>
        <div class="opacy_bg_02">
            <div class="container">
                <h1>Service Categories</h1>
                <div class="crumbs">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li>/</li>
                        <li>Service Categories</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <section class="content-central">
        <div class="content_info">
            <div class="paddings-mini">
                <div class="container">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-6">Add New Service Category</div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <form wire:submit.prevent="storeServiceCategory" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="name">Category Name</label>
                                            <input type="text" class="form-control" wire:model="name" wire:keyup="generateSlug" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="image">Category Image</label>
                                            <input type="file" class="form-control" wire:model="image" accept="image/*" required>
                                            @error('image') 
                                                <span class="text-danger">{{ $message }}</span> 
                                            @enderror
                                            @if($image)
                                                <img src="{{ $image->temporaryUrl() }}" width="100" class="mt-2">
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add Category</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row portfolioContainer">
                        <div class="col-xs-12 col-md-12 profile1">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $startNumber = ($scategories->currentPage() - 1) * $scategories->perPage() + 1;
                                    @endphp
                                    @foreach ($scategories as $index => $scategory)
                                        <tr>
                                            <td>{{$startNumber + $index}}</td>
                                            <td><img src="{{asset('images/categories')}}/{{$scategory->image}}" width="60"/> </td>
                                            <td>{{$scategory->name}}</td>
                                            <td>{{$scategory->slug}}</td>
                                            <td>
                                                <button wire:click="deleteServiceCategory({{$scategory->id}})" class="btn btn-danger btn-sm" onclick="confirm('Are you sure you want to delete this category?') || event.stopImmediatePropagation()">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$scategories->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>                 
</div>
