<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	@if (isset($lang) && $lang == 'ar')
		<body style="text-align: right">
	@else
		<body>
	@endif
		<div>
		    {!!$body!!}
		</div>
	</body>
</html>