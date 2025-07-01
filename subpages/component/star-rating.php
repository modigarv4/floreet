<?php
$name = $name ?? 'rating';
$max = $max ?? 5;
?>
<div class="rating" role="radiogroup" aria-label="Star rating">
    <?php for ($i = 1; $i <= $max; $i++): ?>
        <input value="<?= $i ?>" name="<?= $name ?>" id="star<?= $i ?>" type="radio" />
        <label for="star<?= $i ?>" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>" role="radio" aria-checked="false" tabindex="0">
            <svg
                stroke-linejoin="round"
                stroke-linecap="round"
                stroke-width="2"
                stroke="#000000"
                fill="none"
                viewBox="0 0 24 24"
                height="35"
                width="35"
                xmlns="http://www.w3.org/2000/svg"
                class="svgOne">
                <polygon
                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            <svg
                stroke-linejoin="round"
                stroke-linecap="round"
                stroke-width="2"
                stroke="#000000"
                fill="none"
                viewBox="0 0 24 24"
                height="35"
                width="35"
                xmlns="http://www.w3.org/2000/svg"
                class="svgTwo">
                <polygon
                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            <div class="ombre"></div>
        </label>
    <?php endfor; ?>
</div>