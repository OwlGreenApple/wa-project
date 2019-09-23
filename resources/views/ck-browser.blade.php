<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="csrf_token" content = "{{ csrf_token() }}">
    <title>Example: Browsing Files</title>
	<script type="text/javascript" src="{{asset('assets/js/jquery.min.js')}}"></script>
    <script>
        // Helper function to get parameters from the query string.
        function getUrlParam( paramName ) {
            var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' );
            var match = window.location.search.match( reParam );

            return ( match && match.length > 1 ) ? match[1] : null;
        }
        // Simulate user action of selecting a file to be returned to CKEditor.
        function returnFileUrl( url ) {
            var funcNum = getUrlParam( 'CKEditorFuncNum' );
            var fileUrl = url;
            window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );
            window.close();
        }
    </script>
</head>
<body>

	@foreach($data as $row)
		<img onclick="returnFileUrl('{{url('/public/images')}}/{{$row}}')" src="{{url('/public/ckfinder/'.$folder.'')}}/{{$row}}" />
			<!--<input type="radio" name="filename" value="{{$row}}"/>-->
			<button class="del" data="{{$row}}">Delete</button>
	@endforeach
   <!--<button onclick="returnFileUrl()">Select File</button>-->
   
   <script type="text/javascript">
		$(document).ready(function(){
			 deleteImage();
		});
   
		function deleteImage()
		{
			$(".del").click(function(e){
				e.preventDefault();
				var filename = $(this).attr('data');
				$.ajax({
					header : $("meta[name='csrf_token']").attr('content'),
					type : 'GET',
					url : "{{route('ckdelete')}}",
					data : {'filename' : filename},
					dataType : 'json',
					success : function(response){
						alert(response.msg);
					}
				});
			});
		}
   </script>
   
</body>
</html>