@extends('admin.layouts.base')

@section('contents')
    {{-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif --}}
    <h1 class="text-center text-danger p-3">Modify Project</h1>
    <form class="w-75 m-auto" method="POST" action="{{ route('admin.projects.update', ['project' => $project]) }}" enctype="multipart/form-data" novalidate>
        @method('put')
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                value="{{ old('title', $project->title) }}">

                @error('title')
                    {{ $message }}
                @enderror
        </div>

        <div class="input-group mb-3">
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <label class="input-group-text  @error('image') is-invalid @enderror" for="image">Upload</label>
            @error('image')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-select @error('type_id') is-invalid @enderror"  id="type" name="type_id">
                <option selected>Change type</option>

                @foreach ($types as $type)
                    <option
                        value="{{ $type->id }}"
                        @if ($project->type && old('type_id', $project->type->id) == $type->id) selected @endif
                    >{{ $type->name }}</option>
                @endforeach
            </select>
            @error('type_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <h5>Technologies</h5>
            @foreach($technologies as $technology)
                <div class="mb-3 form-check">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="technology{{ $technology->id }}"
                        name="technologies[]"
                        value="{{ $technology->id }}"
                        @if (in_array($technology->id, old('technologies', $project->technologies->pluck('id')->all()))) checked @endif
                    >
                    <label class="form-check-label" for="technology{{ $technology->id }}">{{ $technology->name }}</label>
                </div>
            @endforeach

            {{-- @dump($errors->get('technologies.*')) --}}
            {{-- @error('technologies')
                <div class="">
                    {{ $message }}
                </div>
            @enderror --}}
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" rows="3"
                name="description" value="{{ old('description', $project->description) }}">@error('description'){{ $message }}@enderror
            </textarea>

        </div>

        <div class="mb-3">
            <label for="link_github" class="form-label">link_github</label>
            <input type="text" class="form-control @error('link_github') is-invalid @enderror" id="link_github" name="link_github"
                value="{{ old('link_github',  $project->link_github) }}">

                @error('link_github')
                    {{ $message }}
                @enderror
        </div>




        <button type="submit" class="btn btn-primary">Salva</button>
    </form>
@endsection
