<footer class="main-footer d-flex justify-content-between">
    <strong>
        {{ Carbon\Carbon::now()->format('d-m-Y') }}
    </strong>
    <strong>
        <a class="ml-auto" href="{{ url('/') }}">
            Booking Tiket SpeedBoat
        </a>
    </strong>
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0
    </div>
</footer>
