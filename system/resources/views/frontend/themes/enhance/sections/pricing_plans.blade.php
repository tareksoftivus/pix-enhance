@include('frontend.themes.enhance.sections.pricing', [
    'section' => $section,
    'pricingPlans' => $pricingPlans ?? collect(),
])
