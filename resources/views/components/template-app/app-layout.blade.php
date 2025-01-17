<!DOCTYPE html>
<html lang="en">

<x-template-app.app-head />

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper">
            <header>
                <x-template-app.app-header />
            </header>
            <x-template-app.app-menu />
            <x-template-other.app-spinner />
            {{$slot}}
            <footer />
        </div>
    </div>
</body>

<x-template-app.app-library />
<x-template-app.app-message />
<x-template-app.app-password />

</html>