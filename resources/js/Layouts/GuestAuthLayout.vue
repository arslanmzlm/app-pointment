<template>
    <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
        <div>
            <a href="/">
                <img
                    alt="Logo"
                    class="h-40 w-40 fill-current text-gray-500"
                    src="@/Images/logo/icon.png"
                />
            </a>
        </div>

        <main class="mt-6 w-full">
            <slot></slot>
        </main>
    </div>
</template>
