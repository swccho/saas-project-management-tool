<template>
  <section id="pricing" class="border-t border-gray-200 bg-gray-50 py-20 sm:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
          Simple pricing
        </h2>
        <p class="mt-4 text-lg text-gray-600">
          Choose the plan that fits your team.
        </p>
      </div>
      <div class="mt-16 grid gap-8 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900">Free</h3>
          <p class="mt-2 text-3xl font-bold text-gray-900">$0</p>
          <p class="mt-1 text-sm text-gray-500">per month</p>
          <ul class="mt-6 space-y-3 text-sm text-gray-600">
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Up to 3 workspaces
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Unlimited projects & tasks
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Team collaboration
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Basic analytics
            </li>
          </ul>
          <router-link
            to="/register"
            class="mt-8 block w-full rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
          >
            Get started
          </router-link>
        </div>
        <div class="relative rounded-xl border-2 border-indigo-500 bg-white p-8 shadow-lg">
          <div class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-indigo-600 px-3 py-1 text-xs font-medium text-white">
            Popular
          </div>
          <h3 class="text-lg font-semibold text-gray-900">Pro</h3>
          <p class="mt-2 text-3xl font-bold text-gray-900">$12</p>
          <p class="mt-1 text-sm text-gray-500">per user / month</p>
          <ul class="mt-6 space-y-3 text-sm text-gray-600">
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Unlimited workspaces
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Advanced analytics
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Priority support
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Custom branding
            </li>
          </ul>
          <router-link
            to="/register"
            class="mt-8 block w-full rounded-lg bg-indigo-600 py-3 text-center text-sm font-medium text-white transition-colors hover:bg-indigo-700"
          >
            Start free trial
          </router-link>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900">Enterprise</h3>
          <p class="mt-2 text-3xl font-bold text-gray-900">Custom</p>
          <p class="mt-1 text-sm text-gray-500">contact us</p>
          <ul class="mt-6 space-y-3 text-sm text-gray-600">
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Everything in Pro
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              SSO & SAML
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Dedicated support
            </li>
            <li class="flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />
              Custom integrations
            </li>
          </ul>
          <router-link
            to="/register"
            class="mt-8 block w-full rounded-lg border border-gray-300 bg-white py-3 text-center text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
          >
            Contact sales
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
</script>
