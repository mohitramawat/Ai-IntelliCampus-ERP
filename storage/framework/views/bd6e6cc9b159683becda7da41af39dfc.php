<header class="bg-brand-surface border-b border-brand-border px-4 sm:px-6 py-3.5 sticky top-0 z-10 shadow-card">
    <div class="flex items-center justify-between gap-4">

        
        <div class="flex items-center gap-3 min-w-0">
            
            <button @click="sidebarOpen = true"
                    class="lg:hidden w-9 h-9 flex items-center justify-center rounded-xl bg-brand-muted border border-brand-border text-brand-sub hover:text-brand-accent hover:border-brand-accent transition-colors">
                <span class="material-symbols-outlined text-xl">menu</span>
            </button>

            
            <div class="min-w-0">
                <h1 class="text-base font-bold text-brand-text truncate"><?php echo e($title ?? 'Dashboard'); ?></h1>
                <p class="text-xs text-brand-sub hidden sm:block">IntelliCampus ERP</p>
            </div>
        </div>

        
        <div class="flex items-center gap-3 flex-shrink-0">
            
            <div class="hidden sm:flex flex-col items-end leading-tight">
                <span class="text-sm font-semibold text-brand-text">
                    <?php echo e(auth()->check() ? auth()->user()->name : 'Guest'); ?>

                </span>
                <span class="text-xs font-medium text-brand-accent capitalize">
                    <?php echo e(auth()->check() ? ucfirst(auth()->user()->roles->first()?->name ?? 'User') : 'User'); ?>

                </span>
            </div>

            
            <div class="w-10 h-10 rounded-xl border-2 border-brand-accent/40 overflow-hidden shadow-card cursor-pointer hover:border-brand-accent hover:shadow-accent transition-all flex-shrink-0" style="width: 40px; height: 40px;">
                <?php if(auth()->check() && auth()->user()->student && auth()->user()->student->profile_picture): ?>
                    <img src="<?php echo e(asset('storage/' . auth()->user()->student->profile_picture)); ?>" 
                         class="w-full h-full object-cover"
                         style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div class="w-full h-full bg-brand-acents flex items-center justify-center">
                        <span class="text-sm font-bold text-brand-accent">
                            <?php echo e(auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U'); ?>

                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</header>
<?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/components/topbar.blade.php ENDPATH**/ ?>