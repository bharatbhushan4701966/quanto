<?php foreach ($service_lists as $item): ?>
        <?php
        $query = new WP_Query([
            'post_type' => 'quanto_service',
            'post__in' => [$item['select_post']],
            'post_status' => 'publish',
        ]);
        while ($query->have_posts()): $query->the_post();
        ?>
        <div class="col-md-6 col-xl-4 col-xxl-3 fade-anim" data-delay="0.30" data-direction="right" >
            <div class="quanto-service-box6 bg-color-white move-anim">
                <div class="service-icon">
                    <?php if (!empty($item['service_icon_select']) && $item['service_icon_select'] === 'quanto_service_image' && !empty($item['quanto_service_image']['url'])): ?>
                            <img src="<?php echo esc_url($item['quanto_service_image']['url']); ?>" alt="service-icon">
                        <?php elseif (!empty($item['quanto_service_icon'])): ?>
                            <?php \Elementor\Icons_Manager::render_icon($item['quanto_service_icon'], ['aria-hidden' => 'true']); ?>
                        <?php endif; ?>
                    </div>
                <div class="service-content">
                <?php 
                    $service_url = !empty($item['service_custom_link']['url']) ? $item['service_custom_link']['url'] : get_the_permalink();
                    $target = !empty($item['service_custom_link']['is_external']) ? ' target="_blank"' : '';
                    $nofollow = !empty($item['service_custom_link']['nofollow']) ? ' rel="nofollow"' : '';
                ?>
                <h5> <a href="<?php echo esc_url($service_url); ?>"<?php echo $target . $nofollow; ?>><?php the_title(); ?></a></h5>
                <p><?php echo esc_html(!empty($item['service_discription_text']) ? $item['service_discription_text'] : ''); ?></p>
                </div>
                <?php if (!empty($item['show_service_feature']) && $item['show_service_feature'] === 'yes'): ?>
                    <div class="service-list">
                        <?php echo wp_kses_post($item['service_feature_text']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; wp_reset_postdata(); ?>
 <?php endforeach; ?>