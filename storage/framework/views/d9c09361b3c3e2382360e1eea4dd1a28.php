

<?php $__env->startSection('content'); ?>

<div class="mb-5">
    <a href="<?php echo e(route('admin-management.index')); ?>"
        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Admin Management
    </a>
</div>

<div class="max-w-2xl">
    <div class="mb-6 flex items-center gap-4">
        <div class="h-12 w-12 overflow-hidden rounded-full border border-gray-200 dark:border-gray-600">
            <img src="<?php echo e($editAdmin->avatar_url); ?>" alt="<?php echo e($editAdmin->name); ?>" class="h-full w-full object-cover" />
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit <?php echo e($editAdmin->name); ?></h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Super Admin · Approved <?php echo e($editAdmin->approved_at?->format('M d, Y')); ?></p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
        <form method="POST" action="<?php echo e(route('admin-management.update', $editAdmin)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>

            <?php if($errors->any()): ?>
            <div class="mb-5 rounded-lg bg-red-50 p-3 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400">
                <?php echo e($errors->first()); ?>

            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                    <input type="text" name="fname" value="<?php echo e(old('fname', $editAdmin->first_name)); ?>"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-sm focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                    <input type="text" name="lname" value="<?php echo e(old('lname', $editAdmin->last_name)); ?>"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-sm focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $editAdmin->email)); ?>"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-sm focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-600 dark:bg-gray-900 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $editAdmin->phone)); ?>"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
                    <input type="text" name="bio" value="<?php echo e(old('bio', $editAdmin->bio)); ?>"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10" />
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="<?php echo e(route('admin-management.index')); ?>"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\AdminView\resources\views/pages/admin-management/edit.blade.php ENDPATH**/ ?>