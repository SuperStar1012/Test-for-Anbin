<html>

<head>
    <title>Test Project</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="<?php echo asset('css/home.css'); ?>" type="text/css">
</head>

<body>
    <div class="container">
        <form action="{{ route('fileUpload') }}" method="post" class="header_view" enctype="multipart/form-data">
            @csrf
            <h3>Select file/Drag and drop</h3>
            <input type="file" class="choose_button" name="file" />
            <input type="submit" class="upload_button" value="Upload" onclick="upload()" />
        </form>
        <div class="content">
            <table>
                <thead>
                    <th>Time</th>
                    <th>File Name</th>
                    <th>Status</th>
                </thead>
                <tbody>
                    @foreach ($files as $file)
                        <tr>
                            <td>{{ $file['time']}}</td>
                            <td>{{ $file['name'] }}</td>
                            <td clss="status">{{ $file['status'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.0.0/dist/echo.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.1.3/socket.io.js"></script>
<script src="<?php echo asset('js/home.js'); ?>"></script>

</html>
