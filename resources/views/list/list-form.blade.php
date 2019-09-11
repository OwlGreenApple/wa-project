@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a href="{{route('home')}}" class="nav-link">Back Home</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!-- end navbar -->

<div class="container mb-2">
    <!-- add list-->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Create List</b></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif 
                    @if (session('error'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                     <form method="POST" action="{{ route('addlist') }}">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">No WA</label>

                            <div class="col-md-8">
                               @if($data->count() > 0)
                                    <select name="wa_number" class="form-control">
                                        @foreach($data as $row)
                                            <option value="{{$row->wa_number}}">       {{$row->wa_number}}
                                            </option>
                                        @endforeach
                                    </select>
                               @else
                                No Numbers
                               @endif
                            </div>
                        </div>  
                        
                        <div class="form-group">
                            <div class="col-md-12">
                                 <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
                            </div>
                        </div> 
                        <!-- submit button -->
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Add List
                                </button>
                            </div>
                        </div>
                     </form>
                     <!-- end form -->

                </div>
            </div>
        </div>
    </div>
<!-- end container -->   
</div>

<script type="text/javascript">
    /* CKEditor */
    var editor = CKEDITOR.replace( 'editor1',{
        extraPlugins: ['filebrowser','colorbutton','justify','image2','font'],
        removePlugins : 'image',
    });
    CKFinder.setupCKEditor( editor );

    CKEDITOR.editorConfig = function( config ) {
        config.extraPlugins = 'filebrowser,colorbutton,justify,image2,font';
        config.removePlugins = 'image';
    };
</script>
@endsection
