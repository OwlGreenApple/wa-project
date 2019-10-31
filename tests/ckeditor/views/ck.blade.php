<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
		 <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" />
		 <link href="{{ asset('assets/ckeditor/content.css') }}" rel="stylesheet" type="text/css" />
		 
		 <!-- js -->
		  <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
		  <script type="text/javascript" src="{{ asset('assets/ckeditor/ckeditor.js') }}" ></script>
		 
		  <script src="{{ asset('js/app.js') }}" type="text/js"></script>
    </head>
    <body>
  
            <div class="content">
			<form>
				   <textarea name="editor1" id="editor1" rows="10" cols="80">
					This is my textarea to be replaced with CKEditor.
				</textarea>
			</form>	
            </div>

		<script>
			// Replace the <textarea id="editor1"> with a CKEditor
			// instance, using default configuration.
			/*var editor = CKEDITOR.replace( 'editor1',{
				 filebrowserBrowseUrl: "{{ route('ckbrowse') }}",
				// filebrowserBrowseUrl: '{{ asset("assets/ckeditor/plugins/ckfinder/ckfinder.html") }}',
				 //filebrowserUploadUrl: '{{ asset("assets/ckeditor/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files")}}',
			} );
			*/
			CKEDITOR.replace( 'editor1',{
				filebrowserBrowseUrl: "{{ route('ckbrowse') }}",
				filebrowserUploadUrl: "{{ route('ckupload') }}",
				extraPlugins : [ 'image2','uploadimage'],
				removePlugins : 'image'
			});
			CKEDITOR.editorConfig = function( config ) {
				 config.extraPlugins = ['image2','uploadimage'];
				 config.removePlugins = 'image';
			};
		
		</script>
    </body>
</html>
