<template>
  <div class="min-h-screen bg-white">
    <LandingNavbar />
    <main>
      <HeroSection />
      <FeaturesSection />
      <ScreenshotsSection />
      <HowItWorksSection />
      <PricingSection />
      <BenefitsSection />
      <CtaSection />
    </main>
    <LandingFooter />
  </div>
</template>

<script setup>
import LandingNavbar from '../components/LandingNavbar.vue';
import HeroSection from '../components/HeroSection.vue';
import FeaturesSection from '../components/FeaturesSection.vue';
import ScreenshotsSection from '../components/ScreenshotsSection.vue';
import HowItWorksSection from '../components/HowItWorksSection.vue';
import PricingSection from '../components/PricingSection.vue';
import BenefitsSection from '../components/BenefitsSection.vue';
import CtaSection from '../components/CtaSection.vue';
import LandingFooter from '../components/LandingFooter.vue';
</script>
