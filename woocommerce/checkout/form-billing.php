<?php
/**
 * Checkout Billing Form Template Override
 * Exact grouped UI: NAME, ADDRESS, LOCATION, EMAIL ADDRESS, PHONE NUMBER
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cmr-custom-billing-wrapper">
    <!-- 1. NAME -->
    <div class="cmr-field-group">
        <label class="cmr-group-label">NAME</label>
        <div class="cmr-grid-2">
            <p class="form-row form-row-first" id="billing_first_name_field">
                <input type="text" class="input-text" name="billing_first_name" id="billing_first_name" placeholder="First" value="<?php echo esc_attr( $checkout->get_value( 'billing_first_name' ) ); ?>" autocomplete="given-name">
            </p>
            <p class="form-row form-row-last" id="billing_last_name_field">
                <input type="text" class="input-text" name="billing_last_name" id="billing_last_name" placeholder="Last" value="<?php echo esc_attr( $checkout->get_value( 'billing_last_name' ) ); ?>" autocomplete="family-name">
            </p>
        </div>
    </div>

    <!-- 2. ADDRESS -->
    <div class="cmr-field-group">
        <label class="cmr-group-label">ADDRESS</label>
        <p class="form-row form-row-wide" id="billing_address_1_field">
            <input type="text" class="input-text" name="billing_address_1" id="billing_address_1" placeholder="Street address, P.O. box, company name" value="<?php echo esc_attr( $checkout->get_value( 'billing_address_1' ) ); ?>" autocomplete="address-line1">
        </p>
        <!-- Hidden address 2 for WooCommerce compatibility -->
        <input type="hidden" name="billing_address_2" id="billing_address_2" value="<?php echo esc_attr( $checkout->get_value( 'billing_address_2' ) ); ?>">
    </div>

    <!-- 3. LOCATION -->
    <div class="cmr-field-group">
        <label class="cmr-group-label">LOCATION</label>
        <div class="cmr-grid-2 cmr-location-grid">
            <p class="form-row" id="billing_city_field">
                <input type="text" class="input-text" name="billing_city" id="billing_city" placeholder="City" value="<?php echo esc_attr( $checkout->get_value( 'billing_city' ) ); ?>" autocomplete="address-level2">
            </p>
            <p class="form-row" id="billing_state_field">
                <?php
                $current_cc = $checkout->get_value( 'billing_country' ) ? $checkout->get_value( 'billing_country' ) : WC()->countries->get_base_country();
                $current_r  = $checkout->get_value( 'billing_state' );
                $states     = WC()->countries->get_states( $current_cc );

                if ( is_array( $states ) && empty( $states ) ) {
                    ?>
                    <input type="hidden" name="billing_state" id="billing_state" value="" />
                    <input type="text" class="input-text" placeholder="State" value="" readonly="readonly" />
                    <?php
                } elseif ( is_array( $states ) ) {
                    ?>
                    <select name="billing_state" id="billing_state" class="state_select" data-placeholder="State" autocomplete="address-level1">
                        <option value="">State</option>
                        <?php
                        foreach ( $states as $ckey => $cvalue ) {
                            echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
                        }
                        ?>
                    </select>
                    <?php
                } else {
                    ?>
                    <input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="State" name="billing_state" id="billing_state" autocomplete="address-level1" />
                    <?php
                }
                ?>
            </p>
            <p class="form-row" id="billing_country_field">
                <select name="billing_country" id="billing_country" class="country_to_state country_select" autocomplete="country">
                    <option value="">Country</option>
                    <?php
                    foreach ( WC()->countries->get_allowed_countries() as $ckey => $cvalue ) {
                        echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_cc, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
                    }
                    ?>
                </select>
            </p>
            <p class="form-row" id="billing_postcode_field">
                <input type="text" class="input-text" name="billing_postcode" id="billing_postcode" placeholder="Pincode" value="<?php echo esc_attr( $checkout->get_value( 'billing_postcode' ) ); ?>" autocomplete="postal-code">
            </p>
        </div>
    </div>

    <!-- 4. EMAIL ADDRESS -->
    <div class="cmr-field-group">
        <label class="cmr-group-label">EMAIL ADDRESS</label>
        <p class="form-row form-row-wide" id="billing_email_field">
            <input type="email" class="input-text" name="billing_email" id="billing_email" placeholder="alexander@botanical.com" value="<?php echo esc_attr( $checkout->get_value( 'billing_email' ) ); ?>" autocomplete="email">
        </p>
    </div>

    <!-- 5. PHONE NUMBER -->
    <div class="cmr-field-group">
        <label class="cmr-group-label">PHONE NUMBER</label>
        <div class="cmr-phone-row">
            <div class="cmr-phone-prefix">
                <span id="cmr_phone_code">+91</span>
            </div>
            <p class="form-row form-row-wide cmr-phone-input-wrap" id="billing_phone_field">
                <input type="tel" class="input-text" name="billing_phone" id="billing_phone" placeholder="00000-00000" value="<?php echo esc_attr( $checkout->get_value( 'billing_phone' ) ); ?>" autocomplete="tel">
            </p>
        </div>
    </div>
</div>
