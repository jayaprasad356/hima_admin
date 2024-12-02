@extends('layouts.admin')

@section('title', 'Update Settings')
@section('content-header', 'Update Settings')



@section('content')
<div class="card">
    <div class="card-body">
      

        <form action="{{ route('news.update', $news->id) }}" method="POST">
        @csrf
        @method('POST')

            <div class="form-group">
                <label for="privacy_policy">Privacy Policy</label>
                <textarea name="privacy_policy" id="privacy_policy" class="form-control ckeditor-content" rows="10" required>{!! $news->privacy_policy !!}</textarea>
            </div>

            <div class="form-group">
                <label for="support_mail">Support Mail</label>
                <input type="email" class="form-control" id="support_mail" name="support_mail" value="{{ $news->support_mail }}" required>
            </div>

            <div class="form-group">
                <label for="demo_video">Demo Video</label>
                <input type="text" class="form-control" id="demo_video" name="demo_video" value="{{ $news->demo_video }}" required>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
<script src="//cdn.ckeditor.com/4.21.0/full-all/ckeditor.js"></script>
<script>
    // Replace CKEditor for privacy_policy and terms_conditions textareas
    document.addEventListener('DOMContentLoaded', function () {
        CKEDITOR.replace('privacy_policy', {
            extraPlugins: 'colorbutton'
        });
        CKEDITOR.replace('terms_conditions', {
            extraPlugins: 'colorbutton'
        });
        CKEDITOR.replace('refund_policy', {
            extraPlugins: 'colorbutton'
        });
    });
</script>
@endsection
