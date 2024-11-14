@foreach ($page_builder ?? [] as $set)
    @includeFirstSafe(['page_builder.' . $set['type'], 'statamic-page-builder-kit::page_builder.' . $set['type']], $set)
@endforeach
