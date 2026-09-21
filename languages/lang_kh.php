<?php
/**
 * ============================================================================
 * Khmer Language Dictionary (ភាសាខ្មែរ)
 * File: languages/lang_kh.php
 * Description: Stores all Khmer UI labels and translations in an associative array.
 * ============================================================================
 */

$lang = array();

// ----------------------------------------------------------------------------
// 1. Site Header & Navigation
// ----------------------------------------------------------------------------
$lang['site_title'] = "វេទិកាព័ត៌មាន";
$lang['site_tagline'] = "សារព័ត៌មានឯករាជ្យ និងការវិភាគស៊ីជម្រៅ";
$lang['cda_badge'] = "ម៉ាស៊ីនចែកចាយមាតិកា (CDA)";
$lang['cma_link'] = "បន្ទះគ្រប់គ្រង CMS";
$lang['search_placeholder'] = "ស្វែងរកអត្ថបទស៊ើបអង្កេត ប្រធានបទ...";
$lang['search_results_for'] = "លទ្ធផលស្វែងរកសម្រាប់";
$lang['clear_search'] = "សម្អាត";
$lang['search_btn'] = "ស្វែងរក";
$lang['get_feed_cta'] = "ទទួលព័ត៌មានឥតគិតថ្លៃ";
$lang['all_stories'] = "អត្ថបទទាំងអស់";
$lang['multi_template_active'] = "ប្រព័ន្ធពហុពុម្ពគំរូសកម្ម";
$lang['breaking'] = "ព័ត៌មានបន្ទាន់";

// ----------------------------------------------------------------------------
// 2. Homepage & Feed Stream
// ----------------------------------------------------------------------------
$lang['latest_stream'] = "បណ្តុំព័ត៌មានចុងក្រោយ";
$lang['read_story'] = "អានអត្ថបទ";
$lang['read_full_article'] = "អានអត្ថបទពេញ";
$lang['no_articles_found'] = "មិនរកឃើញអត្ថបទ";
$lang['no_articles_desc'] = "មិនមានអត្ថបទព័ត៌មានដែលបានផ្សព្វផ្សាយត្រូវគ្នានឹងការស្វែងរករបស់អ្នកទេ។";
$lang['return_home'] = "ត្រឡប់ទៅអត្ថបទទាំងអស់";
$lang['showing_reports'] = "បង្ហាញ :count របាយការណ៍ដែលបានផ្សព្វផ្សាយ";
$lang['total_readers'] = ":count អ្នកអាន";

// ----------------------------------------------------------------------------
// 3. Sidebar & Navigation Widgets
// ----------------------------------------------------------------------------
$lang['daily_digest'] = "ព័ត៌មានប្រចាំថ្ងៃ";
$lang['editorial_dispatch'] = "ការបញ្ជូនព័ត៌មានផ្ទាល់";
$lang['digest_desc'] = "ទទួលការវិភាគ និងប្រភពព័ត៌មានដែលបានផ្ទៀងផ្ទាត់ដោយផ្ទាល់ក្នុងប្រអប់សំបុត្ររបស់អ្នក។";
$lang['join_free_feed'] = "ចុះឈ្មោះទទួលព័ត៌មាន";
$lang['most_read'] = "អត្ថបទដែលមានអ្នកអានច្រើន";
$lang['live_traffic'] = "ចរាចរណ៍ផ្ទាល់";
$lang['explore_topics'] = "រករកប្រធានបទ";

// ----------------------------------------------------------------------------
// 4. Subscription Modal
// ----------------------------------------------------------------------------
$lang['feed_sub_title'] = "ការចុះឈ្មោះទទួលព័ត៌មាន";
$lang['modal_headline'] = "ទទួលការជូនដំណឹងព័ត៌មានទាន់ហេតុការណ៍";
$lang['modal_desc'] = "ចុះឈ្មោះដើម្បីទទួលការជូនដំណឹងភ្លាមៗនៅពេលមានរបាយការណ៍ស៊ើបអង្កេត ឬបទវិចារណកថាថ្មីៗ។";
$lang['email_label'] = "អាសយដ្ឋានអ៊ីមែល";
$lang['topic_pref_label'] = "ជម្រើសប្រធានបទ (មិនជម្រុញ)";
$lang['all_topics_option'] = "គ្រប់ប្រធានបទ និងព័ត៌មានបន្ទាន់";
$lang['btn_confirm_sub'] = "បញ្ជាក់ការចុះឈ្មោះ";
$lang['privacy_guaranteed'] = "ការពារឯកជនភាព";
$lang['direct_feed_endpoint'] = "ចំណុចភ្ជាប់ព័ត៌មានផ្ទាល់";

// ----------------------------------------------------------------------------
// 5. Citation Card & Source Details
// ----------------------------------------------------------------------------
$lang['verified_citation'] = "ប្រភពឯកសារដែលបានផ្ទៀងផ្ទាត់";
$lang['editorial_integrity'] = "ស្តង់ដារសុចរិតភាពសារព័ត៌មាន";
$lang['citation_desc'] = "អត្ថបទនេះដកស្រង់ចេញពីកំណត់ត្រាបឋម និងឯកសារស្រាវជ្រាវដែលបានផ្ទៀងផ្ទាត់៖";
$lang['inspect_reference'] = "ពិនិត្យមើលប្រភពដើម";

// ----------------------------------------------------------------------------
// 6. Article Layout Blueprints
// ----------------------------------------------------------------------------
$lang['standard_layout'] = "ពុម្ពគំរូបទដ្ឋាន";
$lang['investigative_layout'] = "ពុម្ពគំរូបទស៊ើបអង្កេត";
$lang['opinion_layout'] = "ពុម្ពគំរូបទវិចារណកថា";
$lang['special_report'] = "របាយការណ៍ស៊ើបអង្កេតពិសេស";
$lang['further_investigations'] = "ការស៊ើបអង្កេតបន្ថែម";
$lang['opinion_perspective'] = "មតិ និងទស្សនៈ";
$lang['more_perspectives'] = "ទស្សនៈបន្ថែម";
$lang['support_investigative'] = "គាំទ្រសារព័ត៌មានស៊ើបអង្កេតឯករាជ្យ";
$lang['support_desc'] = "ចុះឈ្មោះអ៊ីមែលរបស់អ្នកដើម្បីទទួលការជូនដំណឹងនៅពេលមានការស៊ើបអង្កេតថ្មីៗ។";

// ----------------------------------------------------------------------------
// 7. Admin Panel & Control Dashboard
// ----------------------------------------------------------------------------
$lang['editorial_overview'] = "ទិដ្ឋភាពទូទៅនៃបណ្តាញព័ត៌មាន";
$lang['welcome_back'] = "សូមស្វាគមន៍មកវិញ";
$lang['draft_new_article'] = "សរសេរអត្ថបទថ្មី";
$lang['total_articles'] = "អត្ថបទសរុប";
$lang['published_live'] = "ផ្សាយផ្ទាល់";
$lang['drafts_pending'] = "សេចក្តីព្រាង";
$lang['active_subscribers'] = "អ្នកជាវសកម្ម";
$lang['publication_repository'] = "បណ្ណសារព័ត៌មាន";
$lang['publication_repository_sub'] = "គ្រប់គ្រងអត្ថបទដែលបានផ្សព្វផ្សាយ កែប្រែមាតិកា ឬផ្លាស់ប្តូរពុម្ពគំរូប្លង់";
$lang['article_title'] = "ចំណងជើងអត្ថបទ";
$lang['category'] = "ជំពូក/ប្រធានបទ";
$lang['template_blueprint'] = "ពុម្ពគំរូប្លង់";
$lang['status'] = "ស្ថានភាព";
$lang['views'] = "ការទស្សនា";
$lang['actions'] = "សកម្មភាព";
$lang['published'] = "ផ្សាយរួច";
$lang['draft'] = "សេចក្តីព្រាង";
$lang['archived'] = "បណ្ណសារ";
$lang['template_ratio'] = "ផលធៀបពុម្ពគំរូ";
$lang['template_ratio_sub'] = "ការបែងចែកតាមពុម្ពគំរូប្លង់ ៣ ប្រភេទ";
$lang['traffic_impressions'] = "ចរាចរណ៍អត្ថបទ និងការទស្សនា";
$lang['traffic_impressions_sub'] = "ការបែងចែកការទស្សនាទំព័រផ្ទាល់ប្រចាំថ្ងៃ";
$lang['top_stories'] = "អត្ថបទកំពូលៗ";
$lang['30_days'] = "៣០ ថ្ងៃ";
$lang['tmpl_standard_name'] = "ទម្រង់បទដ្ឋាន ២ជួរឈរ";
$lang['tmpl_investigative_name'] = "ទម្រង់ស៊ើបអង្កេតស៊ីជម្រៅ";
$lang['tmpl_opinion_name'] = "ទម្រង់បទវិចារណកថា";
$lang['filter_all'] = "ទាំងអស់";
$lang['search_repo_placeholder'] = "ស្វែងរកចំណងជើង ឬប្រធានបទ...";
$lang['ajax_feed_sub'] = "ការចុះឈ្មោះទទួលព័ត៌មាន AJAX";
$lang['live_feed'] = "ព័ត៌មានផ្ទាល់";
$lang['no_subscribers_yet'] = "មិនទាន់មានការចុះឈ្មោះពីអ្នកអាននៅឡើយទេ។";
$lang['blueprint_engine_title'] = "ម៉ាស៊ីនពុម្ពគំរូប្លង់ឌីណាមិក";
$lang['blueprint_engine_desc'] = "គ្រប់អត្ថបទដែលបានផ្សព្វផ្សាយត្រូវបានផ្គូផ្គងជាមួយពុម្ពគំរូប្លង់ពិសេស៖";
$lang['blueprint_standard_desc'] = "ទម្រង់បទដ្ឋាន ២ជួរឈរ ជាមួយផ្ទាំងចំហៀងអន្តរកម្ម";
$lang['blueprint_investigative_desc'] = "ទម្រង់អានជម្រៅ ១ជួរឈរ ជាមួយសម្រង់សម្ដី និងប្រភពដើម";
$lang['blueprint_opinion_desc'] = "ទម្រង់បទវិចារណកថា បង្ហាញអ្នកព័ត៌មាន និងរូបថត";
$lang['recent_subscribers'] = "អ្នកជាវថ្មីៗ";
$lang['all_categories'] = "គ្រប់ប្រធានបទ";
$lang['admin_portal'] = "បន្ទះគ្រប់គ្រង CMS";
$lang['staff_login'] = "ច្រកចូលប្រព័ន្ធសម្រាប់បុគ្គលិក";
$lang['username_label'] = "ឈ្មោះអ្នកប្រើ ឬ អ៊ីមែល";
$lang['password_label'] = "ពាក្យសម្ងាត់";
$lang['login_btn'] = "ចូលប្រើប្រព័ន្ធ";
$lang['default_credentials'] = "គណនីបុគ្គលិកគំរូ";
$lang['logout'] = "ចាកចេញ";
$lang['live_public_site'] = "គេហទំព័រសាធារណៈផ្សាយផ្ទាល់ (CDA)";
$lang['management'] = "ការគ្រប់គ្រង";
$lang['categories'] = "ប្រភេទព័ត៌មាន";
$lang['staff_users'] = "អ្នកប្រើប្រាស់ / បុគ្គលិក";
$lang['subscribers'] = "អ្នកជាវព័ត៌មាន";

// ----------------------------------------------------------------------------
// 8. Article Form Labels
// ----------------------------------------------------------------------------
$lang['edit_article_title'] = "កែប្រែអត្ថបទ";
$lang['draft_post_title'] = "សរសេរអត្ថបទផ្សព្វផ្សាយថ្មី";
$lang['back_to_dashboard'] = "ត្រឡប់ទៅផ្ទាំងគ្រប់គ្រង";
$lang['cancel'] = "បោះបង់";
$lang['publish_save'] = "ផ្សព្វផ្សាយ / រក្សាទុកព្រាង";
$lang['update_article'] = "ធ្វើបច្ចុប្បន្នភាពអត្ថបទ";
$lang['url_slug_label'] = "អាសយដ្ឋានតំណអចិន្ត្រៃយ៍ (Slug)";
$lang['auto_generate'] = "បង្កើតស្វ័យប្រវត្តិ";
$lang['summary_label'] = "សេចក្តីសង្ខេបអត្ថបទ";
$lang['summary_placeholder'] = "សេចក្តីសង្ខេបខ្លីៗដែលត្រូវបង្ហាញនៅលើទំព័រដើម...";
$lang['article_body_label'] = "ខ្លឹមសារអត្ថបទពេញលេញ";
$lang['content_placeholder'] = "សរសេរ ឬបិទភ្ជាប់ខ្លឹមសារអត្ថបទពេញលេញនៅទីនេះ...";
$lang['media_citations_title'] = "ប្រព័ន្ធផ្សព្វផ្សាយ និងព័ត៌មានលម្អិតនៃប្រភពដើម";
$lang['video_embed_label'] = "តំណភ្ជាប់វីដេអូ (YouTube Embed ឬ តំណ MP4)";
$lang['ref_name_label'] = "ឈ្មោះប្រភពឯកសារដើម";
$lang['ref_url_label'] = "តំណភ្ជាប់អាសយដ្ឋានប្រភពដើម";
$lang['publishing_control_title'] = "ការគ្រប់គ្រងការផ្សព្វផ្សាយ";
$lang['post_status'] = "ស្ថានភាពអត្ថបទ";
$lang['news_category'] = "ប្រធានបទព័ត៌មាន";
$lang['select_category_option'] = "-- ជ្រើសរើសប្រធានបទ --";
$lang['assigned_author'] = "អ្នកនិពន្ធដែលបានចាត់តាំង";
$lang['flag_breaking'] = "កំណត់ជាព័ត៌មានបន្ទាន់ទាន់ហេតុការណ៍";
$lang['featured_image_label'] = "រូបភាពគម្របតំណាង";
$lang['upload_cover'] = "បង្ហោះរូបភាពគម្រប (អតិបរមា 5MB)";
$lang['current_image'] = "រូបភាពបច្ចុប្បន្ន";
$lang['permitted_formats'] = "ទម្រង់ឯកសារដែលអនុញ្ញាត: JPG, PNG, WEBP.";
$lang['rich_editor_badge'] = "កម្មវិធីកែសម្រួលអត្ថបទ";
$lang['multimedia_embeds_title'] = "ប្រព័ន្ធផ្សព្វផ្សាយ & Embeds អន្តរកម្ម";
$lang['audio_embed_label'] = "URL ផតខាស / របាយការណ៍អូឌីយ៉ូ";
$lang['audio_embed_hint'] = "គាំទ្រ URL embed Spotify / SoundCloud ឬតំណ MP3 ផ្ទាល់។";
$lang['gallery_label'] = "វិចិត្រសាលរូបថតអន្តរកម្ម (URL រូបភាព មួយក្នុងមួយជួរ)";
$lang['gallery_hint'] = "បញ្ចូល URL រូបភាពដែលបំបែកដោយជួរថ្មីដើម្បីបង្ហាញ gallery រូបថតក្នុងអត្ថបទ។";
$lang['verified_citations_title'] = "ប្រភពឯកសារដែលបានផ្ទៀងផ្ទាត់";
$lang['quill_placeholder'] = "សរសេរខ្លឹមសារអត្ថបទពេញលេញរបស់អ្នកនៅទីនេះ...";

// ----------------------------------------------------------------------------
// 9. Footer & Language Labels
// ----------------------------------------------------------------------------
$lang['layout_blueprints_title'] = "ពុម្ពគំរូប្លង់ច្រើនប្រភេទ";
$lang['register_sub_shortcut'] = "ចុះឈ្មោះទទួលព័ត៌មាន";
$lang['rights_reserved'] = "ប្រព័ន្ធគ្រប់គ្រងមាតិកា NewsPlatform។ ស្ថាបត្យកម្ម PHP 8.2+។";
$lang['footer_desc'] = "ប្រព័ន្ធគ្រប់គ្រងមាតិកាកម្រិតវិជ្ជាជីវៈ ដែលត្រូវបានបង្កើតឡើងដោយឡែកពីគ្នារវាងការគ្រប់គ្រង (CMA) និងការចែកចាយ (CDA) ជាមួយពុម្ពគំរូប្លង់ច្រើនប្រភេទ។";
$lang['footer_tmpl_1'] = "ពុម្ពគំរូទី១៖ បទដ្ឋាន — ទម្រង់ព័ត៌មាន ២ជួរឈរ ជាមួយផ្ទាំងចំហៀងអន្តរកម្ម។";
$lang['footer_tmpl_2'] = "ពុម្ពគំរូទី២៖ ស៊ើបអង្កេត — ទម្រង់អានជម្រៅ ១ជួរឈរ ជាមួយសម្រង់សម្ដី និងប្រភពដើម។";
$lang['footer_tmpl_3'] = "ពុម្ពគំរូទី៣៖ វិចារណកថា — ប្លង់បង្ហាញអ្នកព័ត៌មាន និងបទវិចារណកថា។";
$lang['sub_desc_footer'] = "ចូលរួមជាមួយអ្នកសារព័ត៌មាន និងអ្នកស្រាវជ្រាវជាង ១៥,០០០+ នាក់ ដើម្បីទទួលសេចក្តីសង្ខេបព័ត៌មានប្រចាំថ្ងៃ។";
$lang['lang_en'] = "English";
$lang['lang_km'] = "ភាសាខ្មែរ (Khmer)";
$lang['lang_kh'] = "ភាសាខ្មែរ (Khmer)";

// Dynamic Article Component Labels
$lang['about_columnist'] = "អំពីអ្នកវិចារណកថា :name";
$lang['subscribe_columnist_feed'] = "ចុះឈ្មោះជាវព័ត៌មានពី :name";
$lang['columnist_commentary'] = "បទវិចារណកថាតាមជ្រុងជ្រោយ";
$lang['embedded_video_report'] = "របាយការណ៍វីដេអូ";
$lang['audio_report_podcast'] = "របាយការណ៍អូឌីយ៉ូ / ផតខាស";
$lang['photo_gallery'] = "វិចិត្រសាលរូបថត";
$lang['photos'] = "រូបថត";
$lang['published_date'] = "ផ្សាយនៅថ្ងៃទី :date";
$lang['min_read'] = "រយៈពេលអាន :min នាទី";
$lang['related_coverage'] = "អត្ថបទព័ត៌មានពាក់ព័ន្ធ";

return $lang;
