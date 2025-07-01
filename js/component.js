
document.querySelectorAll('.rating').forEach(group => {
  const labels = group.querySelectorAll('label');
  const inputs = group.querySelectorAll('input');

  // Handle arrow key navigation
  labels.forEach((label, idx) => {
    label.addEventListener('keydown', (e) => {
      let targetIndex = null;

      if (e.key === 'ArrowRight') targetIndex = idx + 1;
      if (e.key === 'ArrowLeft') targetIndex = idx - 1;
      if (targetIndex !== null) {
        e.preventDefault();
        if (labels[targetIndex]) labels[targetIndex].focus();
      }

      if (e.key === ' ' || e.key === 'Enter') {
        e.preventDefault();
        inputs[idx].checked = true;
        updateAria();
        updateVisuals();
      }

    });

    label.addEventListener('click', updateAria);
  });

  function updateAria() {
    labels.forEach((lbl, i) => {
      lbl.setAttribute('aria-checked', inputs[i].checked ? 'true' : 'false');
    });
  }

  updateAria(); // on page load

  // Hover: stroke svgOne from left to hovered
  labels.forEach((label, index) => {
    label.addEventListener('mouseenter', () => {
      labels.forEach((l, i) => {
        const svgOne = l.querySelector('.svgOne');
        if (svgOne) svgOne.style.stroke = i <= index ? 'gold' : '#ccc';

        const ombre = l.querySelector('.ombre');
        if (ombre) ombre.style.opacity = i <= index ? '0.3' : '0';
      });
    });

    label.addEventListener('mouseleave', () => {
      updateVisuals();
    });
  });

  // Selection: apply gold fill, animation, hide outlines, show ombre
  function updateVisuals() {
    let checkedIndex = Array.from(inputs).findIndex(input => input.checked);

    labels.forEach((label, i) => {
      const svgOne = label.querySelector('.svgOne');
      const svgTwo = label.querySelector('.svgTwo');
      const ombre = label.querySelector('.ombre');

      if (i <= checkedIndex) {
        if (svgOne) svgOne.style.stroke = 'transparent';
        if (svgTwo) {
          svgTwo.style.opacity = '1';
          svgTwo.style.animation = 'displayStar 0.5s cubic-bezier(0.75, 0.41, 0.82, 1.2)';
        }
        if (ombre) ombre.style.opacity = '1';
      } else {
        if (svgOne) svgOne.style.stroke = '#ccc';
        if (svgTwo) {
          svgTwo.style.opacity = '0';
          svgTwo.style.animation = 'none';
        }
        if (ombre) ombre.style.opacity = '0';
      }
    });
  }

  inputs.forEach(input => input.addEventListener('change', () => {
    updateAria();
    updateVisuals();
  }));

  // On initial load
  updateVisuals();

});

