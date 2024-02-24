<a href="{{ route('wishlists.index') }}" class="position-relative text-reset btn bg-sm btn-circle btn-icon bg-alter">
    <i class="la la-heart-o opacity-80 fs-20"></i>
    <span class="absolute-top-right" style="top: -5px;right: -5px;">
        @if(Auth::check())
            <span class="badge badge-primary badge-inline badge-pill">{{ count(Auth::user()->wishlists)}}</span>
        @else
            <span class="badge badge-primary badge-inline badge-pill">0</span>
        @endif
    </span>
</a>
