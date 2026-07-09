                <div class="weblazem-tab-content" data-tab-content="hero">
                    <div class="weblazem-admin-card">
                        <h3>بخش هیرو</h3>
                        <?php weblazem_service_landing_admin_field('hero_en_title', 'عنوان انگلیسی'); ?>
                        <?php weblazem_service_landing_admin_image('hero_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_textarea('hero_calligraphy_text', 'متن خوشنویسی (HTML)', 'در صورت نبود تصویر'); ?>
                        <?php weblazem_service_landing_admin_field('hero_title', 'عنوان فارسی'); ?>
                        <?php weblazem_service_landing_admin_textarea('hero_text', 'متن توضیحی'); ?>
                        <?php weblazem_service_landing_admin_image('hero_image', 'تصویر مانیتور / ایلاستریشن'); ?>
                        <h4>شمارنده‌ها</h4>
                        <?php weblazem_service_landing_admin_field('hero_stat1_number', 'شمارنده ۱ — عدد'); ?>
                        <?php weblazem_service_landing_admin_field('hero_stat1_title', 'شمارنده ۱ — عنوان'); ?>
                        <?php weblazem_service_landing_admin_field('hero_stat1_desc', 'شمارنده ۱ — توضیح'); ?>
                        <?php weblazem_service_landing_admin_field('hero_stat2_number', 'شمارنده ۲ — عدد'); ?>
                        <?php weblazem_service_landing_admin_field('hero_stat2_title', 'شمارنده ۲ — عنوان'); ?>
                        <?php weblazem_service_landing_admin_field('hero_stat2_desc', 'شمارنده ۲ — توضیح'); ?>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="portfolio">
                    <div class="weblazem-admin-card">
                        <h3>نمونه‌کارها (Success Stories)</h3>
                        <?php weblazem_service_landing_admin_image('portfolio_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_textarea('portfolio_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_field('portfolio_subtitle', 'زیرعنوان'); ?>
                        <?php weblazem_service_landing_admin_textarea('portfolio_description', 'توضیح'); ?>
                        <?php weblazem_service_landing_admin_field('portfolio_en_label', 'برچسب انگلیسی'); ?>
                        <?php weblazem_service_landing_admin_field('portfolio_count', 'تعداد نمونه‌کار از CPT'); ?>
                        <h4>تب‌های فیلتر</h4>
                        <div id="webdesign-tabs-container">
                            <?php foreach ($tabs as $i => $tab) : weblazem_webdesign_admin_portfolio_tab($i, $tab, $categories); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-webdesign-tab">افزودن تب</button>
                        <h4 style="margin-top:24px;">کارت‌های دستی (اختیاری)</h4>
                        <div id="webdesign-portfolio-items">
                            <?php if (!empty($manual_items)) : foreach ($manual_items as $i => $item) : weblazem_webdesign_admin_portfolio_item($i, $item); endforeach; endif; ?>
                        </div>
                        <button type="button" class="button" id="add-webdesign-portfolio-item">افزودن کارت</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="customers">
                    <div class="weblazem-admin-card">
                        <h3>مشتریان</h3>
                        <?php weblazem_service_landing_admin_image('customers_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_field('customers_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_field('customers_counter', 'شمارنده'); ?>
                        <?php weblazem_service_landing_admin_field('customers_counter_label', 'برچسب انگلیسی شمارنده'); ?>
                        <?php weblazem_service_landing_admin_image('customers_bottom_icon', 'آیکون پایین سکشن'); ?>
                        <h4>لوگوها</h4>
                        <div id="webdesign-logos-container">
                            <?php foreach ($logos as $i => $logo) : weblazem_webdesign_admin_logo_row($i, $logo); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-webdesign-logo">افزودن لوگو</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="splits">
                    <div class="weblazem-admin-card">
                        <h3>بخش‌های دو ستونه</h3>
                        <div id="webdesign-splits-container">
                            <?php foreach ($splits as $i => $split) : weblazem_webdesign_admin_split_row($i, $split); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-webdesign-split">افزودن بخش</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="process">
                    <div class="weblazem-admin-card">
                        <h3>فرآیند کار</h3>
                        <?php weblazem_service_landing_admin_image('process_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_textarea('process_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_field('process_subtitle', 'زیرعنوان'); ?>
                        <?php weblazem_service_landing_admin_textarea('process_description', 'توضیح'); ?>
                        <?php weblazem_service_landing_admin_field('process_start_note', 'یادداشت شروع'); ?>
                        <h4>مراحل</h4>
                        <div id="webdesign-steps-container">
                            <?php foreach ($steps as $i => $step) : ?>
                                <p><input type="text" name="weblazem_webdesign_process_steps[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($step['title']); ?>" placeholder="عنوان مرحله" />
                                <button type="button" class="button webdesign-step-remove">حذف</button></p>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-webdesign-step">افزودن مرحله</button>
                        <h4>CSAT و دکمه‌ها</h4>
                        <?php weblazem_service_landing_admin_field('process_csat_number', 'درصد CSAT'); ?>
                        <?php weblazem_service_landing_admin_field('process_csat_sub', 'زیرنویس CSAT'); ?>
                        <?php weblazem_service_landing_admin_field('process_csat_label', 'برچسب CSAT'); ?>
                        <?php weblazem_service_landing_admin_field('process_btn1_text', 'دکمه چپ — متن'); ?>
                        <?php weblazem_service_landing_admin_field('process_btn1_url', 'دکمه چپ — لینک'); ?>
                        <?php weblazem_service_landing_admin_field('process_btn2_text', 'دکمه راست — متن'); ?>
                        <?php weblazem_service_landing_admin_field('process_btn2_url', 'دکمه راست — لینک'); ?>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="advantages">
                    <div class="weblazem-admin-card">
                        <h3>مزایا</h3>
                        <?php weblazem_service_landing_admin_field('advantages_title', 'عنوان'); ?>
                        <?php weblazem_service_landing_admin_textarea('advantages_subtitle', 'زیرعنوان'); ?>
                        <div id="webdesign-advantages-container">
                            <?php foreach ($advantages as $i => $item) : weblazem_webdesign_admin_advantage_row($i, $item, $icon_choices); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-webdesign-advantage">افزودن مزیت</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="faq">
                    <div class="weblazem-admin-card">
                        <h3>FAQ و تماس</h3>
                        <?php weblazem_service_landing_admin_image('faq_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_textarea('faq_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_service_landing_admin_field('faq_subtitle', 'عنوان FAQ'); ?>
                        <?php weblazem_service_landing_admin_textarea('faq_intro', 'متن مقدمه'); ?>
                        <?php weblazem_service_landing_admin_image('faq_profile_image', 'تصویر پروفایل'); ?>
                        <?php weblazem_service_landing_admin_field('faq_phone', 'شماره تماس'); ?>
                        <?php weblazem_service_landing_admin_field('faq_consult_btn_text', 'متن دکمه مشاوره'); ?>
                        <?php weblazem_service_landing_admin_textarea('faq_footer_text', 'متن پایین کارت پروفایل'); ?>
                        <h4>سوالات</h4>
                        <div id="webdesign-faq-container">
                            <?php foreach ($faq_items as $i => $item) : weblazem_webdesign_admin_faq_row($i, $item); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-webdesign-faq">افزودن سوال</button>
                        <h4 style="margin-top:24px;">کارت‌های خدمات پایین صفحه</h4>
                        <div id="webdesign-service-cards">
                            <?php foreach ($cards as $i => $card) : weblazem_webdesign_admin_service_card($i, $card); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-webdesign-service-card">افزودن کارت</button>
                    </div>
                </div>
