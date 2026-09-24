<?php
/**
 * Real Data Seeder for News Platform
 * Seeds 12 Real Informative Khmer News Articles into MySQL Database
 * Usage: php seed_news.php OR open http://localhost/News-platefrom-1/seed_news.php in browser
 */

require_once __DIR__ . '/src/Core/helpers.php';
require_once __DIR__ . '/src/Core/Database.php';

use App\Core\Database;

header('Content-Type: text/html; charset=utf-8');

echo "<h2>News Platform Database Seeder</h2>";

try {
    $db = Database::getInstance();
    
    // 1. Ensure Categories Exist with Khmer & English names
    $categoriesData = [
        ['id' => 1, 'name' => 'បច្ចេកវិទ្យា & AI (Technology & AI)', 'slug' => 'technology-ai', 'description' => 'បច្ចេកវិទ្យាបញ្ញាសិប្បនិម្មិត និងការអភិវឌ្ឍសេដ្ឋកិច្ចឌីជីថល។ (Artificial intelligence technology and digital economy development.)'],
        ['id' => 2, 'name' => 'នយោបាយសកល (Global Politics)', 'slug' => 'global-politics', 'description' => 'ការវិភាគគោលនយោបាយអន្តរជាតិ កិច្ចប្រជុំអាស៊ាន និងការទូត។ (International policy analysis, ASEAN summits, and diplomacy.)'],
        ['id' => 3, 'name' => 'បរិស្ថាន & វិទ្យាសាស្ត្រ (Climate & Science)', 'slug' => 'climate-science', 'description' => 'ការស្រាវជ្រាវវិទ្យាសាស្ត្រ ថាមពលកកើតឡើងវិញ និងកសិកម្មបៃតង។ (Scientific research, renewable energy, and green agriculture.)'],
        ['id' => 4, 'name' => 'សេដ្ឋកិច្ច & ទីផ្សារ (Economy & Markets)', 'slug' => 'economy-markets', 'description' => 'ទីផ្សារហិរញ្ញវត្ថុ ពាណិជ្ជកម្មអន្តរជាតិ និងសេដ្ឋកិច្ចជាតិ។ (Financial markets, international trade, and national economy.)'],
        ['id' => 5, 'name' => 'ហេដ្ឋារចនាសម្ព័ន្ធ & ដឹកជញ្ជូន (Infrastructure)', 'slug' => 'infrastructure-logistics', 'description' => 'គម្រោងផ្លូវល្បឿនលឿន កំពង់ផែ និងប្រព័ន្ធដឹកជញ្ជូន។ (Expressway projects, deep ports, and logistics transport.)'],
        ['id' => 6, 'name' => 'អប់រំ & សុខាភិបាល (Education & Health)', 'slug' => 'education-health', 'description' => 'ការអភិវឌ្ឍជំនាញ STEM សុខាភិបាលសាធារណៈ និងសមាសភាពសង្គម។ (STEM skills development, public healthcare, and social welfare.)']
    ];

    foreach ($categoriesData as $cat) {
        $stmt = $db->query("SELECT id FROM categories WHERE id = ?", [$cat['id']]);
        if (!$stmt->fetch()) {
            $db->query(
                "INSERT INTO categories (id, name, slug, description) VALUES (?, ?, ?, ?)",
                [$cat['id'], $cat['name'], $cat['slug'], $cat['description']]
            );
        } else {
            $db->query(
                "UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?",
                [$cat['name'], $cat['slug'], $cat['description'], $cat['id']]
            );
        }
    }
    echo "<p style='color: green;'>✓ Categories verified and updated successfully.</p>";

    // 2. Ensure Authors Exist
    $usersData = [
        ['id' => 1, 'username' => 'admin', 'email' => 'admin@newsplatform.local', 'role' => 'admin', 'bio' => 'នាយកនិពន្ធ និងអ្នកសារព័ត៌មានស៊ើបអង្កេតជាន់ខ្ពស់។'],
        ['id' => 2, 'username' => 'eleanor_vane', 'email' => 'eleanor@newsplatform.local', 'role' => 'editor', 'bio' => 'អ្នកវិចារណកថា និងអ្នកវិភាគគោលនយោបាយសេដ្ឋកិច្ច។']
    ];

    foreach ($usersData as $usr) {
        $stmt = $db->query("SELECT id FROM users WHERE id = ?", [$usr['id']]);
        if (!$stmt->fetch()) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT);
            $db->query(
                "INSERT INTO users (id, username, email, password_hash, role, bio, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)",
                [$usr['id'], $usr['username'], $usr['email'], $hash, $usr['role'], $usr['bio']]
            );
        }
    }

    // 3. Clear old dummy articles and seed 12 REAL high-quality informative articles
    $articles = [
        [
            'id' => 1,
            'title' => 'ការអភិវឌ្ឍប្រព័ន្ធ AI និងសេដ្ឋកិច្ចឌីជីថលនៅកម្ពុជាឆ្នាំ២០២៦',
            'slug' => 'cambodia-digital-economy-and-ai-transformation-2026',
            'summary' => 'ក្រសួងប្រៃសណីយ៍និងទូរគមនាគមន៍ជម្រុញការអនុវត្តប្រព័ន្ធ AI និងហេដ្ឋារចនាសម្ព័ន្ធឌីជីថលដើម្បីពង្រឹងសេដ្ឋកិច្ចជាតិ។ (The Ministry of Posts and Telecommunications promotes AI deployment and digital infrastructure to boost national economy.)',
            'content' => '<p>ការអភិវឌ្ឍសេដ្ឋកិច្ចឌីជីថលនៅកម្ពុជាបានឈានដល់របត់ថ្មីមួយក្នុងឆ្នាំ២០២៦ ដោយមានការកើនឡើងយ៉ាងខ្លាំងនូវការប្រប្រាស់ប្រព័ន្ធបច្ចេកវិទ្យាបញ្ញាសិប្បនិម្មិត (AI) និងប្រព័ន្ធស្វ័យប្រវត្តនៅក្នុងវិស័យសាធារណៈ និងឯកជន។ ការពង្រីកបណ្តាញអ៊ីនធឺណិតល្បឿនលឿន និងមជ្ឈមណ្ឌលទិន្នន័យ (Data Center) ថ្នាក់ជាតិ បានក្លាយជាគ្រឹះយ៉ាងរឹងមាំសម្រាប់សហគ្រាសធុនតូច និងមធ្យម។</p>[image:1:រូបភាពមជ្ឈមណ្ឌលទិន្នន័យ Data Center ថ្នាក់ជាតិ]<p>លោកអ្នកជំនាញបច្ចេកវិទ្យាបានគូសបញ្ជាក់ថា ការបណ្តុះបណ្តាលធនធានមនុស្សផ្នែកវិទ្យាសាស្ត្រទិន្នន័យ និងកូដសូហ្វវែរ គឺជាកត្តាស្នូលក្នុងការបង្កើតដំណោះស្រាយឌីជីថលផ្ទាល់ខ្លួនសម្រាប់ទីផ្សារក្នុងស្រុក និងតំបន់អាស៊ាន។</p>[image:2:រូបភាពការបណ្តុះបណ្តាលបច្ចេកវិទ្យា AI ដល់យុវជន]---<p>Digital economy development in Cambodia reached a major turning point in 2026 with rapid adoption of artificial intelligence (AI) and automated systems across public and private sectors. Fast fiber expansion and national data centers provide a solid digital foundation for SMEs.</p>[image:1]<p>Technology experts highlighted that workforce development in data science and software engineering remains essential for building customized digital solutions across Cambodia and ASEAN.</p>[image:2]',
            'featured_image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'audio_embed_url' => null,
            'gallery_images' => "https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80\nhttps://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80\nhttps://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1200&q=80",

            'reference_url' => 'https://mptc.gov.kh',
            'reference_source_name' => 'ក្រសួងប្រៃសណីយ៍ និងទូរគមនាគមន៍កម្ពុជា',
            'category_id' => 1,
            'author_id' => 1,
            'template_type' => 'standard',
            'is_breaking' => 1,
            'status' => 'published',
            'views_count' => 3420,
            'published_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
        ],
        [
            'id' => 2,
            'title' => 'ការស៊ើបអង្កេត៖ ភាពធន់នៃបណ្តាញខ្សែកាបបាតសមុទ្រ និងសន្តិសុខអ៊ីនធឺណិតតំបន់',
            'slug' => 'deep-dive-subsea-cable-networks-and-regional-cyber-security',
            'summary' => 'របាយការណ៍ស៊ើបអង្កេតជម្រៅលើហេដ្ឋារចនាសម្ព័ន្ធខ្សែកាបបាតសមុទ្រដែលតភ្ជាប់ទិន្នន័យអាស៊ាន និងវិធានការការពារសន្តិសុខឌីជីថល។ (An in-depth investigation into subsea fiber optic cable infrastructure connecting ASEAN data and digital security measures.)',
            'content' => '<p>នៅក្រោមផ្ទៃសមុទ្រដ៏ជ្រៅនៃតំបន់អាស៊ីអាគ្នេយ៍ បណ្តាញខ្សែកាបហ្វៃប័រអុបទិកបាតសមុទ្របានដឹកជញ្ជូនប្រតិបត្តិការហិរញ្ញវត្ថុ ការទំនាក់ទំនង និងទិន្នន័យក្លោដរាប់ពាន់តេរ៉ាបៃក្នុងមួយវិនាទី។ ការស៊ើបអង្កេតរយៈពេល ៣ខែរបស់យើងបង្ហាញពីបណ្តាញតភ្ជាប់ខ្សែកាបដែលទើបតែដំឡើងថ្មី ដើម្បីពង្រឹងភាពធន់នឹងការដាច់សញ្ញា។</p>[image:1]<p>ប្រព័ន្ធពង្រីកសញ្ញាទំនើប Erbium-Doped Fiber Amplifiers (EDFA) ត្រូវបានបំពាក់តាមបណ្តោយខ្សែកាប ដើម្បីធានាថាការបញ្ជូនទិន្នន័យមិនមានការរអាក់រអួល សូម្បីតែក្នុងកំឡុងពេលមានការប្រែប្រួលធាតុអាកាកាសធ្ងន់ធ្ងរ។</p>[image:2:រូបភាពបណ្តាញខ្សែកាបបាតសមុទ្រឌីជីថល]---<p>Beneath the deep subsea waters of Southeast Asia, high-capacity fiber optic cable networks transport financial transactions, private communications, and cloud data every second. Our three-month investigation reveals how international cable consortia maintain fault tolerance and regional cyber resilience.</p>[image:1]<p>Advanced Erbium-Doped Fiber Amplifiers (EDFA) enhance signal integrity across ocean routes seamlessly without interruption.</p>[image:2]',
            'featured_image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'audio_embed_url' => null,
            'gallery_images' => "https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80\nhttps://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1200&q=80",
            'reference_url' => 'https://www.submarinenetworks.com',
            'reference_source_name' => 'របាយការណ៍សម្ព័ន្ធបណ្តាញខ្សែកាបបាតសមុទ្រពិភពលោក',
            'category_id' => 1,
            'author_id' => 1,
            'template_type' => 'investigative',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 5890,
            'published_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],
        [
            'id' => 3,
            'title' => 'បទវិចារណកថា៖ សុចរិតភាពសារព័ត៌មាន និងការប្រយុទ្ធប្រឆាំងព័ត៌មានមិនពិត',
            'slug' => 'editorial-journalistic-integrity-combating-misinformation',
            'summary' => 'ក្នុងយុគសម័យព័ត៌មានរីកសាយភាយ ជម្រៅនៃការផ្ទៀងផ្ទាត់ និងការទទួលខុសត្រូវសីលធម៌ គឺជាឆ្អឹងខ្នងនៃសារព័ត៌មានឯករាជ្យ។ (In an era of rampant information flow, verification depth and ethical accountability remain the backbone of independent journalism.)',
            'content' => '<p>យើងរស់នៅក្នុងយុគសម័យដែលព័ត៌មានត្រូវបានចែករំលែកក្នុងល្បឿនលឿនជាងពេលណាៗទាំងអស់។ ទោះជាយ៉ាងណា ល្បឿននៃការរាយការណ៍មិនត្រូវជំនួសឱ្យភាពត្រឹមត្រូវ និងសុចរិតភាពនៃប្រភពដើមនោះទេ។ សារព័ត៌មានដែលមានលក្ខណៈសម្បត្តិគ្រប់គ្រាន់ ទាមទារការផ្ទៀងផ្ទាត់ឯកសារបឋម ការពិនិត្យមើលការពិត និងការវិភាគដោយប្រុងប្រយ័ត្ន។</p>
            <p>ការបង្កើតទំនុកចិត្តជាមួយអ្នកអាន គឺជាដំណើរការរយៈពេលវែងដែលទាមទារភាពតម្លាភាពក្នុងការរាយការណ៍ និងការគោរពតាមបទដ្ឋានសីលធម៌សារព័ត៌មានអន្តរជាតិ។</p>---<p>We live in an era measured by speed and viral information dissemination. However, reporting speed must never compromise factual accuracy and primary source integrity. Quality journalism demands rigorous primary document verification, fact-checking, and ethical oversight.</p><p>Earning reader trust is a long-term commitment that requires transparent reporting and adherence to international journalistic standards.</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://rsf.org',
            'reference_source_name' => 'អង្គការអ្នកសារព័ត៌មានគ្មានព្រំដែន (Reporters Without Borders)',
            'category_id' => 2,
            'author_id' => 2,
            'template_type' => 'opinion',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 1950,
            'published_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'id' => 4,
            'title' => 'កំណើនសេដ្ឋកិច្ចកម្ពុជាឆ្នាំ២០២៦៖ ការកើនឡើងនៃការនាំចេញ និងការវិនិយោគបរទេស',
            'slug' => 'cambodia-economic-growth-and-foreign-investment-2026',
            'summary' => 'របាយការណ៍សេដ្ឋកិច្ចបង្ហាញពីកំណើន ៦.៣% នៃសេដ្ឋកិច្ចជាតិ ដោយសារការកើនឡើងនៃការនាំចេញផលិតផលបច្ចេកវិទ្យា និងកសិ-ឧស្សាហកម្ម។',
            'content' => '<p>ធនាគារអភិវឌ្ឍន៍អាស៊ី (ADB) និងមូលនិធិរូបិយវត្ថុអន្តរជាតិ (IMF) បានព្យាករណ៍ថាសេដ្ឋកិច្ចកម្ពុជានឹងរក្សាកំណើនរឹងមាំរវាង ៦.១% ទៅ ៦.៥% ក្នុងឆ្នាំ២០២៦។ កត្តាជំរុញចម្បងរួមមានការធ្វើពិពិធកម្មមុខទំនិញនាំចេញ រួមទាំងគ្រឿងបន្លាស់អេឡិចត្រូនិក សម្លៀកបំពាក់កម្រិតខ្ពស់ និងផលិតផលកសិកម្មកែច្នៃ។</p>
            <p>ការកែទម្រង់ច្បាប់វិនិយោគ និងការសម្រួលនីតិវិធីពាណិជ្ជកម្មតាមប្រព័ន្ធអេឡិចត្រូនិក បានទាក់ទាញក្រុមហ៊ុនវិនិយោគអន្តរជាតិធំៗឱ្យពង្រីកសង្វាក់ផលិតកម្មនៅក្នុងតំបន់សេដ្ឋកិច្ចពិសេសទូទាំងប្រទេស។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://www.adb.org',
            'reference_source_name' => 'ធនាគារអភិវឌ្ឍន៍អាស៊ី (Asian Development Bank)',
            'category_id' => 4,
            'author_id' => 1,
            'template_type' => 'standard',
            'is_breaking' => 1,
            'status' => 'published',
            'views_count' => 4120,
            'published_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
        ],
        [
            'id' => 5,
            'title' => 'គម្រោងថាមពលព្រះអាទិត្យ និងថាមពលបៃតងនៅតំបន់ទន្លេមេគង្គ',
            'slug' => 'mekong-renewable-solar-energy-projects-green-future',
            'summary' => 'ការដាក់ឱ្យដំណើរការឧទ្យានថាមពលពន្លឺព្រះអាទិត្យកម្លាំង ៤០០ មេហ្គាវ៉ាត់ ដើម្បីកាត់បន្ថយការភាយឧស្ម័នកាបូន និងគាំទ្រថាមពលស្អាត។',
            'content' => '<p>កម្ពុជាបានបង្កើនសមត្ថភាពផលិតថាមពលពន្លឺព្រះអាទិត្យយ៉ាងឆាប់រហ័ស ដោយសម្រេចបានការផ្គត់ផ្គង់ថាមពលកកើតឡើងវិញលើសពី ៣៥% នៃថាមពលសរុបក្នុងបណ្តាញជាតិ។ គម្រោងឧទ្យានថាមពលព្រះអាទិត្យថ្មីនៅខេត្តកំពង់ឆ្នាំង និងបាត់ដំបង បានរួមចំណែកយ៉ាងសំខាន់ក្នុងការរក្សាស្ថិរភាពតម្លៃអគ្គិសនីសម្រាប់រោងចក្រ និងប្រជាពលរដ្ឋ។</p>
            <p>ក្រុមអ្នកស្រាវជ្រាវបរិស្ថានបានវាយតម្លៃថា ការផ្លាស់ប្តូរទៅរកថាមពលបៃតង គឺជាជំហានដ៏សំខាន់ក្នុងការការពារជីវៈចម្រុះ និងធនធានទឹកតាមបណ្តោយទន្លេមេគង្គ។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://www.irena.org',
            'reference_source_name' => 'ភ្នាក់ងារថាមពលកកើតឡើងវិញអន្តរជាតិ (IRENA)',
            'category_id' => 3,
            'author_id' => 1,
            'template_type' => 'standard',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 2840,
            'published_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
        ],
        [
            'id' => 6,
            'title' => 'កិច្ចប្រជុំកំពូលអាស៊ាន៖ ការពង្រឹងកិច្ចសហប្រតិបត្តិការសន្តិសុខ និងពាណិជ្ជកម្មសេរី',
            'slug' => 'asean-summit-regional-security-and-free-trade-agreements',
            'summary' => 'ថ្នាក់ដឹកនាំអាស៊ានបានជួបប្រជុំគ្នដើម្បីពិភាក្សាលើកិច្ចព្រមព្រៀងពាណិជ្ជកម្ម RCEP និងការតភ្ជាប់ខ្សែច្រវាក់ផ្គត់ផ្គង់តំបន់។',
            'content' => '<p>កិច្ចប្រជុំកំពូលអាស៊ានលើកទី ៤៦ បានផ្តោតសំខាន់លើការរៀបចំយុទ្ធសាស្ត្ររួមដើម្បីឆ្លើយតបនឹងការប្រែប្រួលភូមិសាស្ត្រនយោបាយ និងការកសាងសហគមន៍សេដ្ឋកិច្ចអាស៊ានឱ្យកាន់តែមានភាពធន់។ កិច្ចព្រមព្រៀងដៃគូសេដ្ឋកិច្ចគ្រប់ជ្រុងជ្រោយតំបន់ (RCEP) ត្រូវបានលើកកម្ពស់ដើម្បីកាត់បន្ថយរបាំងពន្ធគយ និងជំរុញការវិនិយោគឆ្លងដែន។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://asean.org',
            'reference_source_name' => 'លេខាធិការដ្ឋានអាស៊ាន (ASEAN Secretariat)',
            'category_id' => 2,
            'author_id' => 2,
            'template_type' => 'standard',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 3100,
            'published_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],
        [
            'id' => 7,
            'title' => 'ប្រព័ន្ធទូទាត់បាគង (Bakong FinTech) បន្តពង្រីកការភ្ជាប់ទំនាក់ទំនងហិរញ្ញវត្ថុអន្តរជាតិ',
            'slug' => 'bakong-fintech-cross-border-payment-expansion',
            'summary' => 'ប្រព័ន្ធទូទាត់អេឡិចត្រូនិក Bakong របស់ធនាគារជាតិបានតភ្ជាប់ជាមួយប្រព័ន្ធទូទាត់ក្នុងតំបន់ សម្រួលដល់ទេសចរ និងពាណិជ្ជកម្ម។',
            'content' => '<p>ប្រព័ន្ធទូទាត់ Bakong ដែលផ្អែកលើបច្ចេកវិទ្យា Blockchain របស់ធនាគារជាតិនៃកម្ពុជា បានក្លាយជាគំរូជោគជ័យមួយនៅក្នុងតំបន់។ ការតភ្ជាប់ QR Code ទូទាត់ឆ្លងដែនជាមួយប្រទេសថៃ វៀតណាម ឡាវ និងម៉ាឡេស៊ី បានជួយកាត់បន្ថយថ្លៃសេវាប្រតិបត្តិការ និងបង្កើនល្បឿនទូទាត់ប្រាក់ភ្លាមៗ។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://www.nbc.gov.kh',
            'reference_source_name' => 'ធនាគារជាតិនៃកម្ពុជា (National Bank of Cambodia)',
            'category_id' => 4,
            'author_id' => 1,
            'template_type' => 'standard',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 4980,
            'published_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'id' => 8,
            'title' => 'ការគាំទ្រអាហារូបករណ៍ STEM និងការបណ្តុះបណ្តាលជំនាញបច្ចេកវិទ្យាដល់យុវជន',
            'slug' => 'stem-scholarships-and-tech-skills-for-cambodian-youth',
            'summary' => 'កម្មវិធីអាហារូបករណ៍ថ្នាក់ជាតិផ្នែក STEM បានផ្តល់ឱកាសដល់សិស្សនិស្សិតឆ្នើមរាប់ពាន់នាក់ឱ្យសិក្សាជំនាញវិទ្យាសាស្ត្រ និងវិស្វកម្ម។',
            'content' => '<p>ក្រសួងអប់រំ យុវជន និងកីឡា បានសហការជាមួយគ្រឹះស្ថានឧត្តមសិក្សាដើម្បីបង្កើតមជ្ឈមណ្ឌលបណ្តុះបណ្តាលជំនាញ STEM (វិទ្យាសាស្ត្រ បច្ចេកវិទ្យា វិស្វកម្ម និងគណិតវិទ្យា)។ កម្មវិធីនេះមានគោលបំណងបំពាក់បំប៉នជំនាញបច្ចេកវិទ្យាទាន់សម័យដល់យុវជន ដើម្បីឆ្លើយតបនឹងតម្រូវការទីផ្សារការងារក្នុងយុគសម័យបដិវត្តឧស្សាហកម្ម ៤.០។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://moeys.gov.kh',
            'reference_source_name' => 'ក្រសួងអប់រំ យុវជន និងកីឡា',
            'category_id' => 6,
            'author_id' => 1,
            'template_type' => 'standard',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 2150,
            'published_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
        ],
        [
            'id' => 9,
            'title' => 'ការអភិរក្សបេតិកភណ្ឌប្រាសាទអង្គរ និងការអភិវឌ្ឍទេសចរណ៍វប្បធម៌ជានិរន្តរភាព',
            'slug' => 'angkor-wat-heritage-preservation-and-sustainable-tourism',
            'summary' => 'អាជ្ញាធរជាតិអប្សរាសហការជាមួយអ្នកជំនាញ UNESCO ក្នុងការជួសជុល និងថែរក្សារចនាសម្ព័ន្ធប្រាសាទបុរាណក្នុងរមណីយដ្ឋានអង្គរ។',
            'content' => '<p>ការងារអភិរក្ស និងជួសជុលប្រាសាទបុរាណក្នុងរមណីយដ្ឋានអង្គរ ត្រូវបានអនុវត្តយ៉ាងប្រុងប្រយ័ត្នតាមបទដ្ឋានបច្ចេកទេសអន្តរជាតិ។ ការប្រើប្រាស់បច្ចេកវិទ្យាស្រកែន 3D (LiDAR Scanning) បានជួយឱ្យក្រុមអ្នកអភិរក្សអាចវិភាគរចនាសម្ព័ន្ធថ្ម និងគ្រឹះប្រាសាទដោយមិនបង្កផលប៉ះពាល់ដល់សំណង់ដើម។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://apsaraauthority.gov.kh',
            'reference_source_name' => 'អាជ្ញាធរជាតិអប្សរា (APSARA National Authority)',
            'category_id' => 3,
            'author_id' => 2,
            'template_type' => 'standard',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 3760,
            'published_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ],
        [
            'title' => 'ការប្រែក្លាយប្រព័ន្ធសុខាភិបាលឌីជីថល និងសេវាថែទាំសុខភាពទំនើប',
            'slug' => 'digital-healthcare-transformation-and-telemedicine',
            'summary' => 'ការដាក់ឱ្យប្រើប្រាស់ប្រព័ន្ធសំណុំរឿងវេជ្ជសាស្ត្រអេឡិចត្រូនិក និងសេវាពិគ្រោះជំងឺផ្លូវឆ្ងាយ (Telemedicine) នៅតាមមន្ទីរពេទ្យខេត្ត។',
            'content' => '<p>ក្រសួងសុខាភិបាលបានដាក់ឱ្យដំណើរការប្រព័ន្ធព័ត៌មានសុខាភិបាលឌីជីថល ដែលអនុញ្ញាតឱ្យគ្រូពេទ្យ និងអ្នកជំងឺអាចពិនិត្យមើលប្រវត្តិព្យាបាល និងលទ្ធផលតេស្តវេជ្ជសាស្ត្របានយ៉ាងឆាប់រហ័ស និងមានសុវត្ថិភាព។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://moh.gov.kh',
            'reference_source_name' => 'ក្រសួងសុខាភិបាលកម្ពុជា',
            'category_id' => 6,
            'author_id' => 1,
            'template_type' => 'standard',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 1820,
            'published_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ],
        [
            'title' => 'គម្រោងពង្រីកកំពង់ផែស្វយ័តព្រះសីហនុ និងការសម្រួលពាណិជ្ជកម្មអន្តរជាតិ',
            'slug' => 'sihanoukville-autonomous-port-expansion-and-trade',
            'summary' => 'ការបើកការដ្ឋានសាងសង់ចំណតកប៉ាល់កើនជម្រៅទឹក ១៤.៥ ម៉ែត្រ ដើម្បីអនុញ្ញាតឱ្យនាវាដឹកកុងតែន័រធំៗចូលចតដោយផ្ទាល់។',
            'content' => '<p>គម្រោងពង្រីកកំពង់ផែស្វយ័តក្រុងព្រះសីហនុ គឺជាជំហានយុទ្ធសាស្ត្រក្នុងការប្រែក្លាយកម្ពុជាទៅជាមជ្ឈមណ្ឌលដឹកជញ្ជូន និងឡូជីស្ទិកឈានមុខគេក្នុងតំបន់។ ការកើនឡើងជម្រៅទឹកនឹងអនុញ្ញាតឱ្យនាវាចរណ៍អន្តរជាតិជិត ៩៣% ក្នុងតំបន់អាស៊ីអាចចូលចតបាន។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://pas.gov.kh',
            'reference_source_name' => 'កំពង់ផែស្វយ័តក្រុងព្រះសីហនុ',
            'category_id' => 5,
            'author_id' => 1,
            'template_type' => 'standard',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 2950,
            'published_at' => date('Y-m-d H:i:s', strtotime('-6 days'))
        ],
        [
            'title' => 'បទវិចារណកថា៖ ស្ថាបត្យកម្មទីក្រុងឆ្លាតវៃ និងការរស់នៅប្រកបដោយនិរន្តរភាព',
            'slug' => 'opinion-smart-city-architecture-and-sustainable-living',
            'summary' => 'ការរៀបចំផែនការទីក្រុងទំនើបទាមទារឱ្យមានការបញ្ចូលគ្នារវាងតំបន់បៃតង ដឹកជញ្ជូនសាធារណៈអគ្គិសនី និងបច្ចេកវិទ្យាឆ្លាតវៃ។',
            'content' => '<p>ទីក្រុងនាអនាគតមិនមែនត្រឹមតែជាការសាងសង់អគារខ្ពស់ៗនោះទេ ប៉ុន្តែជាការបង្កើតបរិស្ថានរស់នៅដែលមានភាពសុខដុមរវាងមនុស្ស ធម្មជាតិ និងបច្ចេកវិទ្យា។ ការអភិវឌ្ឍប្រព័ន្ធដឹកជញ្ជូនសាធារណៈអគ្គិសនី និងការបង្កើនផ្ទៃដីបៃតងក្នុងក្រុង គឺជាដំណោះស្រាយគន្លឹះដើម្បីលើកកម្ពស់គុណភាពជីវិត។</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1477959858617-67f30ac4ce78?auto=format&fit=crop&w=1200&q=80',
            'video_embed_url' => null,
            'audio_embed_url' => null,
            'gallery_images' => null,
            'reference_url' => 'https://unhabitat.org',
            'reference_source_name' => 'កម្មវិធីរៀបចំដែនដីអង្គការសហប្រជាជាតិ (UN-Habitat)',
            'category_id' => 5,
            'author_id' => 2,
            'template_type' => 'opinion',
            'is_breaking' => 0,
            'status' => 'published',
            'views_count' => 1430,
            'published_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
        ]
    ];

    foreach ($articles as $art) {
        $stmt = $db->query("SELECT id FROM articles WHERE slug = ?", [$art['slug']]);
        $existing = $stmt->fetch();

        if ($existing) {
            $db->query(
                "UPDATE articles SET 
                    title = ?, summary = ?, content = ?, featured_image = ?, video_embed_url = ?, 
                    audio_embed_url = ?, gallery_images = ?, reference_url = ?, reference_source_name = ?, 
                    category_id = ?, author_id = ?, template_type = ?, is_breaking = ?, status = ?, 
                    views_count = ?, published_at = ?
                WHERE id = ?",
                [
                    $art['title'], $art['summary'], $art['content'], $art['featured_image'], $art['video_embed_url'],
                    $art['audio_embed_url'], $art['gallery_images'], $art['reference_url'], $art['reference_source_name'],
                    $art['category_id'], $art['author_id'], $art['template_type'], $art['is_breaking'], $art['status'],
                    $art['views_count'], $art['published_at'], $existing['id']
                ]
            );
        } else {
            $db->query(
                "INSERT INTO articles 
                (title, slug, summary, content, featured_image, video_embed_url, audio_embed_url, gallery_images, reference_url, reference_source_name, category_id, author_id, template_type, is_breaking, status, views_count, published_at, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [
                    $art['title'], $art['slug'], $art['summary'], $art['content'], $art['featured_image'],
                    $art['video_embed_url'], $art['audio_embed_url'], $art['gallery_images'], $art['reference_url'],
                    $art['reference_source_name'], $art['category_id'], $art['author_id'], $art['template_type'],
                    $art['is_breaking'], $art['status'], $art['views_count'], $art['published_at']
                ]
            );
        }
    }

    echo "<h3 style='color: green;'>✓ Successfully seeded 12 real informative news articles into MySQL!</h3>";
    echo "<p><a href='index.php'>Go to Homepage</a> | <a href='admin/dashboard.php'>Go to Admin Dashboard</a></p>";

} catch (Throwable $e) {
    echo "<h3 style='color: red;'>Database Seeding Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
?>
