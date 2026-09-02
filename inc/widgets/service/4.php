<div class="col-12">
    <?php foreach ($service_lists as $item): ?>
    <?php
    $query = new WP_Query([
        'post_type' => 'quanto_service',
        'post__in' => [$item['select_post']],
        'post_status' => 'publish',
    ]);
    while ($query->have_posts()): $query->the_post();
    ?>
    <div class="quanto-service-box4 fade-anim bg-light rounded-4 p-4 p-md-5 mb-4 border-0 d-flex flex-column align-items-start">
        <div class="quanto-iconbox-icon mb-4">
            <?php if (!empty($item['service_icon_select']) && $item['service_icon_select'] === 'quanto_service_image' && !empty($item['quanto_service_image']['url'])): ?>
                    <img src="<?php echo esc_url($item['quanto_service_image']['url']); ?>" alt="service-icon" style="width: 38px; height: 38px; object-fit: contain;">
            <?php elseif (!empty($item['quanto_service_icon'])): ?>
                    <?php \Elementor\Icons_Manager::render_icon($item['quanto_service_icon'], ['aria-hidden' => 'true']); ?>
            <?php endif; ?>
        </div>
        <h3 class="service-title fw-semibold text-dark mb-3 fs-4"><?php the_title(); ?></h3>
        <div class="service-info w-100">
            <p class="mb-4 text-dark"><?php echo esc_html(!empty($item['service_discription_text']) ? $item['service_discription_text'] : ''); ?></p>
            <?php 
                $service_url = !empty($item['service_custom_link']['url']) ? $item['service_custom_link']['url'] : get_the_permalink();
                $target = !empty($item['service_custom_link']['is_external']) ? ' target="_blank"' : '';
                $nofollow = !empty($item['service_custom_link']['nofollow']) ? ' rel="nofollow"' : '';
            ?>
            <a class="quanto-link-btn text-dark fw-semibold text-decoration-underline" href="<?php echo esc_url($service_url); ?>"<?php echo $target . $nofollow; ?>>
                <?php echo esc_html(!empty($item['service_btn_text']) ? $item['service_btn_text'] : 'Explore'); ?>
                <span class="ms-1">
                <i class="fa-solid fa-arrow-right arry1"></i>
                <i class="fa-solid fa-arrow-right arry2"></i>
                </span>
            </a>
        </div>
    </div>
    <?php endwhile; wp_reset_postdata(); ?>
    <?php endforeach; ?>
</div>
    