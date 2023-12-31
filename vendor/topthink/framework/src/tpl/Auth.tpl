<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$msg}</title>
    <link rel="icon" type="image/png" href="//auth.nanyinet.com/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="//auth.nanyinet.com/static/css/style.tailwind.css">
</head>
<body>
<div id="page-container" class="flex flex-col mx-auto w-full min-h-screen min-w-80 bg-white text-gray-800 ">
    <main id="page-content" class="flex flex-auto flex-col max-w-full bg-gray-50">
        <div class="flex-grow flex flex-col items-center justify-center bg-white overflow-hidden">
            <div class="flex-none container xl:max-w-6xl mx-auto p-8 text-center">
                <a class="group inline-flex items-center space-x-1 font-bold text-lg text-gray-700" href="/">
                    <img src="//auth.nanyinet.com/logo.png" alt="Nathan_Auth" style="width: 150.8px; height: 100%;" draggable="false" onerror="onerror=null;">
                </a>
            </div>
            <div class="flex-grow flex items-center justify-center">
                <div class="group relative px-4 lg:px-8">
                    <div class="absolute inset-0 -m-16 bg-indigo-100 rounded-3xl transform -rotate-2 translate-y-1 scale-105 transition ease-out duration-500 group-hover:-rotate-1">
                    </div>
                    <div class="absolute inset-0 -m-16 bg-brand-100 rounded-3xl transform rotate-2 translate-y-1 scale-105 transition ease-out duration-500 group-hover:rotate-1">
                    </div>
                    <div class="absolute inset-0 -m-16 bg-white rounded-md shadow-subtle">
                    </div>
                    <div class="relative">
                        <div>
                            <div class="mb-8 text-center">
                                <div class="mb-5 text-red-500">
                                    <svg class="fa-duo fa-exclamation-triagle inline-block w-20 h-20" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M569.52 440L329.58 24c-18.44-32-64.69-32-83.16 0L6.48 440c-18.42 31.94 4.64 72 41.57 72h479.89c36.87 0 60.06-40 41.58-72zM288 448a32 32 0 1132-32 32 32 0 01-32 32zm38.24-238.41l-12.8 128A16 16 0 01297.52 352h-19a16 16 0 01-15.92-14.41l-12.8-128A16 16 0 01265.68 192h44.64a16 16 0 0115.92 17.59z" opacity=".4"/><path d="M310.32 192h-44.64a16 16 0 00-15.92 17.59l12.8 128A16 16 0 00278.48 352h19a16 16 0 0015.92-14.41l12.8-128A16 16 0 00310.32 192zM288 384a32 32 0 1032 32 32 32 0 00-32-32z" class="fa-primary"/></svg>
                                </div>
                                <h1 class="text-2xl font-bold mb-1">
                                    你所浏览的页面暂时无法访问
                                </h1>
                                <h2 class="text-gray-500">
                                    <b>{$msg}</b>
                                </h2>
                            </div>
                        </div>
                        <div class="text-center space-x-1 sm:space-x-2">
                            <a href="http://auth.nanyinet.com" target="_blank" class="inline-flex justify-center items-center space-x-2 rounded border font-medium focus:outline-none px-4 py-2 leading-6 border-brand-700 bg-brand-700 text-white hover:text-white hover:bg-brand-800 hover:border-brand-800 focus:ring focus:ring-brand-500 focus:ring-opacity-50 active:bg-brand-700">
                                <svg t="1627203648027" class="hi-solid hi-home inline-block w-5 h-5 opacity-60" fill="currentColor" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="9465"><path d="M895.13984 588.99456c0.67072-9.25184 1.024-18.54976 1.024-27.81696A380.60032 380.60032 0 0 0 650.49088 204.8a134.90176 134.90176 0 0 0-268.72832 0 380.59008 380.59008 0 0 0-245.69856 356.38272c0 8.77568 0.39936 17.57184 0.99328 26.32192a135.22944 135.22944 0 0 0 59.3664 256.60928 133.89824 133.89824 0 0 0 53.47328-11.12064 380.81024 380.81024 0 0 0 266.24 109.09184 376.13056 376.13056 0 0 0 265.216-108.20096A134.8352 134.8352 0 0 0 967.68 708.81792a135.37792 135.37792 0 0 0-72.54016-119.82336zM516.1216 148.48a68.75136 68.75136 0 1 1-68.608 68.75648A68.7616 68.7616 0 0 1 516.1216 148.48zM127.83104 708.81792a68.608 68.608 0 1 1 68.608 68.75136 68.75136 68.75136 0 0 1-68.608-68.75136z m175.104 82.80576A134.99392 134.99392 0 0 0 202.752 573.8496a317.30688 317.30688 0 0 1-0.29184-12.672 314.17856 314.17856 0 0 1 190.3616-289.15712 134.70208 134.70208 0 0 0 246.61504 0 314.16832 314.16832 0 0 1 190.35648 289.152c0 4.1728-0.24064 8.3456-0.40448 12.5184a135.10656 135.10656 0 0 0-101.888 219.67872A310.272 310.272 0 0 1 516.1216 875.52a314.25024 314.25024 0 0 1-213.17632-83.89632z m529.7408-14.0544a68.75136 68.75136 0 1 1 68.608-68.75136 68.74624 68.74624 0 0 1-68.59776 68.75136z" p-id="9466"></path></svg>
                                <span>官方授权站</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-none container xl:max-w-6xl mx-auto p-8 text-center text-sm">
                Copyright © {$date}. <strong>NanYiNet</strong> All rights reserved.
            </div>
        </div>
    </main>
</div>
</body>
</html>