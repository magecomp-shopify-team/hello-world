<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel with React</title>
    <meta name="shopify-api-key" content="{{ config('e79d3a95116603eb0a0ced7966c28b51') }}" />
    <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
</head>

<body>
    <div id="app"></div>
    
    @viteReactRefresh
    @vite('resources/react/app.jsx')
</body>

</html>