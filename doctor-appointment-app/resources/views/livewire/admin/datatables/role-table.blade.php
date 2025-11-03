<div class="p-4">
    <h2 class="text-xl font-bold mb-3">Roles</h2>
    <ul class="list-disc pl-5">
        @foreach($roles as $r)
            <li>#{{ $r['id'] }} — {{ $r['name'] }}</li>
        @endforeach
    </ul>
</div>
