<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>IntelliCampus ERP</title>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="antialiased bg-gray-100 min-h-screen flex flex-col items-center justify-center">
        <div class="max-w-7xl mx-auto p-6 lg:p-8 text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">IntelliCampus ERP</h1>
            
            <div class="flex justify-center space-x-4">
                <?php if(Route::has('login')): ?>
                    <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(url('/dashboard')); ?>" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                            
                            
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <p class="text-gray-600 mt-4">Welcome to the College ERP System.</p>
        </div>
    </body>
</html>
<?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\welcome.blade.php ENDPATH**/ ?>