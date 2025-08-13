/**
 * validation.js – Step navigation with HTML5 validation
 *
 * This script handles the multi-step form logic and relies on built-in
 * browser validation. Each step advances only when the current fieldset
 * meets all HTML5 constraints (required, min, max, etc.).
 */

document.addEventListener('DOMContentLoaded', () => {
  const steps = document.querySelectorAll('.form-steps fieldset');
  const nav = document.querySelector('.form-navigation');
  const nextBtn = nav.querySelector('.next-btn');
  const backBtn = nav.querySelector('.back-btn');
  const submitBtn = nav.querySelector('.submit-btn');
  const status = document.getElementById('step-status');

  let currentStep = 0;

  function showStep(idx) {
    steps.forEach((fieldset, i) => {
      fieldset.style.display = i === idx ? 'block' : 'none';
    });

    backBtn.style.display = idx === 0 ? 'none' : '';
    nextBtn.style.display = idx === steps.length - 1 ? 'none' : '';
    submitBtn.style.display = idx === steps.length - 1 ? '' : 'none';

    if (window.updateProgress) {
      window.updateProgress(idx);
    }
    window.dispatchEvent(new CustomEvent('stepChanged', { detail: { currentStep: idx } }));
  }

  nextBtn.addEventListener('click', () => {
    const step = steps[currentStep];
    if (step.checkValidity()) {
      if (status) {
        status.textContent = `Schritt ${currentStep + 1} abgeschlossen`;
        status.classList.remove('hidden');
        setTimeout(() => status.classList.add('hidden'), 2000);
      }
      currentStep++;
      showStep(currentStep);
    } else {
      step.reportValidity();
    }
  });

  backBtn.addEventListener('click', () => {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });

  showStep(currentStep);
});

