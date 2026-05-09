
<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    style="display:none;"
    class="fixed inset-0 z-40 bg-brand-text/20 backdrop-blur-sm lg:hidden"
></div>

<aside
    :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', sidebarCollapsed ? 'lg:w-20' : 'lg:w-64']"
    class="w-64 fixed lg:static inset-y-0 left-0 z-50 bg-brand-surface border-r border-brand-border h-screen flex flex-col shadow-card-md transition-[width,transform] duration-300 ease-in-out"
>
    
    <div class="px-5 py-5 border-b border-brand-border relative flex items-center justify-between flex-shrink-0">
        <a href="<?php echo e(route(auth()->check() ? auth()->user()->dashboard_route : 'login')); ?>"
           class="flex items-center flex-1 overflow-hidden min-w-0"
           :class="sidebarCollapsed ? 'justify-center' : ''">
            <div class="w-9 h-9 min-w-[36px] rounded-xl bg-brand-accent flex items-center justify-center shadow-accent flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 overflow-hidden" x-show="!sidebarCollapsed" x-transition.opacity>
                <span class="text-base font-black text-brand-text tracking-tight leading-tight block">IntelliCampus</span>
                <span class="text-[10px] font-semibold text-brand-accent tracking-widest uppercase">ERP System</span>
            </div>
        </a>

        
        <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="hidden lg:flex items-center justify-center w-6 h-6 rounded-full bg-brand-muted border border-brand-border text-brand-sub hover:bg-brand-accent hover:text-white hover:border-brand-accent transition-all absolute -right-3 top-[1.35rem] shadow-card z-50">
            <span class="material-symbols-outlined text-[14px]" x-text="sidebarCollapsed ? 'chevron_right' : 'chevron_left'"></span>
        </button>

        
        <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-lg text-brand-sub hover:text-brand-text transition-colors">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>

    
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto no-scrollbar">
        <?php
            $roles = auth()->check() ? auth()->user()->roles->pluck('name')->map(fn($r) => strtolower($r))->toArray() : ['student'];
            
            // Sort roles by priority so higher roles control the duplicate items like 'Dashboard'
            $rolePrecedence = ['admin', 'hod', 'accounts', 'writer', 'teacher', 'student'];
            usort($roles, function($a, $b) use ($rolePrecedence) {
                $posA = array_search($a, $rolePrecedence);
                $posB = array_search($b, $rolePrecedence);
                return ($posA === false ? 999 : $posA) <=> ($posB === false ? 999 : $posB);
            });

            $menuItems = [];
            $seenNames = [];

            foreach ($roles as $role) {
                $items = config('sidebar.' . $role, []);
                foreach ($items as $item) {
                    if (!in_array($item['name'], $seenNames)) {
                        $menuItems[] = $item;
                        $seenNames[] = $item['name'];
                    }
                }
            }
        ?>

        <?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($item['submenu'])): ?>
                
                <?php
                    $isSubActive = false;
                    foreach($item['submenu'] as $sub) {
                        if(request()->routeIs($sub['route']) || request()->routeIs($sub['route'] . '.*')) {
                            $isSubActive = true;
                            break;
                        }
                    }
                ?>
                <div x-data="{ open: <?php echo e($isSubActive ? 'true' : 'false'); ?> }" class="w-full">
                    <button @click="open = !open"
                            title="<?php echo e($item['name']); ?>"
                            class="w-full flex items-center rounded-xl transition-all duration-200 group outline-none
                                   <?php echo e($isSubActive ? 'text-brand-accent font-semibold bg-brand-acents/30' : 'text-brand-sub hover:bg-brand-muted hover:text-brand-text'); ?>"
                            :class="sidebarCollapsed ? 'justify-center p-3' : 'px-3 py-2.5'">
                        
                        <span class="material-symbols-outlined text-[20px] flex-shrink-0
                                     <?php echo e($isSubActive ? 'text-brand-accent' : 'text-brand-sub group-hover:text-brand-accent'); ?>"
                              :class="sidebarCollapsed ? '' : 'mr-3'"><?php echo e($item['icon'] ?? 'circle'); ?></span>

                        <span class="text-sm whitespace-nowrap overflow-hidden truncate flex-1 text-left" x-show="!sidebarCollapsed" x-transition.opacity>
                            <?php echo e($item['name']); ?>

                        </span>

                        <span class="material-symbols-outlined text-[18px] transition-transform duration-200"
                              :class="{'rotate-180': open}"
                              x-show="!sidebarCollapsed" x-transition.opacity>expand_more</span>
                    </button>

                    
                    <div x-show="open && !sidebarCollapsed" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="pl-11 pr-2 space-y-1 mt-1 mb-2">
                        <?php $__currentLoopData = $item['submenu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $isActive = request()->routeIs($sub['route']) || request()->routeIs($sub['route'] . '.*'); ?>
                            <a href="<?php echo e(route($sub['route'])); ?>"
                               @click="sidebarOpen = false"
                               class="flex items-center py-2 px-3 rounded-lg text-[13px] transition-all
                                      <?php echo e($isActive ? 'text-brand-accent font-bold bg-brand-acents' : 'text-brand-sub hover:text-brand-text hover:bg-brand-muted'); ?>">
                                <span class="truncate"><?php echo e($sub['name']); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php else: ?>
                
                <?php
                    $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                ?>
                <a href="<?php echo e(route($item['route'])); ?>"
                   @click="sidebarOpen = false"
                   title="<?php echo e($item['name']); ?>"
                   class="flex items-center rounded-xl transition-all duration-200 group
                          <?php echo e($isActive
                              ? 'bg-brand-acents text-brand-accent font-semibold'
                              : 'text-brand-sub hover:bg-brand-muted hover:text-brand-text'); ?>"
                   :class="sidebarCollapsed ? 'justify-center p-3' : 'px-3 py-2.5'">

                    <span class="material-symbols-outlined text-[20px] flex-shrink-0
                                 <?php echo e($isActive ? 'text-brand-accent' : 'text-brand-sub group-hover:text-brand-accent'); ?>"
                          :class="sidebarCollapsed ? '' : 'mr-3'"><?php echo e($item['icon'] ?? 'circle'); ?></span>

                    <span class="text-sm whitespace-nowrap overflow-hidden truncate" x-show="!sidebarCollapsed" x-transition.opacity>
                        <?php echo e($item['name']); ?>

                    </span>
                </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    
    <div class="p-3 border-t border-brand-border flex-shrink-0">
        
        <div class="flex items-center gap-3 px-2 py-2 mb-1 rounded-xl" x-show="!sidebarCollapsed" x-transition.opacity>
            <div class="w-8 h-8 rounded-full bg-brand-accent/10 border border-brand-accent/30 flex items-center justify-center flex-shrink-0">
                <span class="text-xs font-bold text-brand-accent">
                    <?php echo e(auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U'); ?>

                </span>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-brand-text truncate"><?php echo e(auth()->check() ? auth()->user()->name : 'Guest'); ?></p>
                <p class="text-[10px] text-brand-sub truncate"><?php echo e(auth()->check() ? ucfirst(auth()->user()->roles->first()?->name ?? '') : ''); ?></p>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit"
                    title="Logout"
                    class="w-full flex items-center rounded-xl text-brand-sub hover:bg-status-dangers hover:text-status-danger transition-all duration-200 font-medium text-sm"
                    :class="sidebarCollapsed ? 'justify-center p-3' : 'px-3 py-2.5'">
                <span class="material-symbols-outlined text-[20px]" :class="sidebarCollapsed ? '' : 'mr-3'">logout</span>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Logout</span>
            </button>
        </form>
    </div>
</aside>
<?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\components\sidebar.blade.php ENDPATH**/ ?>