<?php

namespace Modules\Quiz\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Quiz\Models\Quiz;
use Modules\Quiz\Models\QuizQuestion;
use Modules\Quiz\Models\QuizQuestionOption;

class CyberSecurityQuizSeeder extends Seeder
{
    /**
     * Seed 10 cyber-security quizzes, each with 10 questions.
     */
    public function run(): void
    {
        $user = User::where('role', 'admin')->first()
            ?? User::first()
            ?? User::factory()->create([
                'name'     => 'Ciso Office',
                'email'    => 'ciso@office.com',
                'password' => bcrypt('password'),
                'role'     => 'admin',
            ]);

        foreach ($this->quizzes() as $index => $quizData) {
            // Every third quiz is timed per-question to exercise both timer modes.
            $perQuestion = $index % 3 === 2;

            $quiz = Quiz::create([
                'user_id'               => $user->id,
                'title'                 => $quizData['title'],
                'description'           => $quizData['description'],
                'marks_per_question'    => 1,
                'duration_type'         => $perQuestion ? 'per_question' : 'full_paper',
                'duration_minutes'      => $perQuestion ? null : 15,
                'duration_per_question' => $perQuestion ? 45 : null,
                'attempts_allowed'      => 2,
                'pass_percentage'       => 60,
                'can_review_paper'      => true,
                'can_view_result'       => true,
                'status'                => 'published',
                'language'              => 'en',
            ]);

            foreach ($quizData['questions'] as $qIndex => $questionData) {
                $question = QuizQuestion::create([
                    'quiz_id'       => $quiz->id,
                    'question_text' => $questionData['text'],
                    'question_type' => $questionData['type'],
                    'order'         => $qIndex + 1,
                ]);

                foreach ($questionData['options'] as $oIndex => $option) {
                    QuizQuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct'  => $option['correct'] ?? false,
                        'order'       => $oIndex + 1,
                    ]);
                }
            }
        }
    }

    /**
     * Helper to build a true/false question.
     */
    private function trueFalse(string $text, bool $answerIsTrue): array
    {
        return [
            'text'    => $text,
            'type'    => 'true_false',
            'options' => [
                ['text' => 'True', 'correct' => $answerIsTrue],
                ['text' => 'False', 'correct' => ! $answerIsTrue],
            ],
        ];
    }

    /**
     * Helper to build a single-choice question.
     * $options is an array of [text, correct?] pairs.
     */
    private function single(string $text, array $options): array
    {
        return [
            'text'    => $text,
            'type'    => 'single',
            'options' => array_map(
                fn ($o) => ['text' => $o[0], 'correct' => $o[1] ?? false],
                $options
            ),
        ];
    }

    /**
     * The 10 cyber-security quizzes.
     */
    private function quizzes(): array
    {
        return [
            [
                'title'       => 'Phishing & Social Engineering Awareness',
                'description' => 'Learn to recognise and respond to phishing, vishing, smishing and other social-engineering attacks.',
                'questions'   => [
                    $this->single('What is phishing?', [
                        ['A method of catching fish online'],
                        ['A cyberattack that uses disguised messages to trick recipients into revealing data', true],
                        ['A type of antivirus software'],
                        ['A secure email protocol'],
                    ]),
                    $this->single('Which is a common sign of a phishing email?', [
                        ['Perfect grammar and a digital signature'],
                        ['A personalised greeting from a known colleague'],
                        ['A sense of urgency demanding immediate action', true],
                        ['A verified sender certificate'],
                    ]),
                    $this->trueFalse('You should click links in unexpected emails to verify if they are legitimate.', false),
                    $this->single('What is "spear phishing"?', [
                        ['Phishing that targets a specific individual or organisation', true],
                        ['Phishing using fishing equipment'],
                        ['Mass phishing sent to random addresses'],
                        ['Phishing that only happens over the phone'],
                    ]),
                    $this->single('A phone-based social-engineering attack is known as:', [
                        ['Smishing'],
                        ['Vishing', true],
                        ['Pharming'],
                        ['Whaling'],
                    ]),
                    $this->single('"Whaling" attacks typically target:', [
                        ['Senior executives and high-profile individuals', true],
                        ['Random new employees'],
                        ['The IT help desk only'],
                        ['External customers only'],
                    ]),
                    $this->single('SMS-based phishing is called:', [
                        ['Vishing'],
                        ['Smishing', true],
                        ['Spamming'],
                        ['Spoofing'],
                    ]),
                    $this->trueFalse('Hovering over a link to preview its real URL before clicking can help detect phishing.', true),
                    $this->single('What should you do if you receive a suspicious email asking for your credentials?', [
                        ['Reply with your credentials'],
                        ['Forward it to all your colleagues'],
                        ['Report it to your IT/security team and do not click', true],
                        ['Click the link to investigate'],
                    ]),
                    $this->single('Social engineering primarily exploits:', [
                        ['Software vulnerabilities'],
                        ['Human psychology and trust', true],
                        ['Network firewalls'],
                        ['Encryption weaknesses'],
                    ]),
                ],
            ],
            [
                'title'       => 'Password Security & Authentication',
                'description' => 'Best practices for strong passwords, password managers and multi-factor authentication.',
                'questions'   => [
                    $this->single('Which of these is the strongest password?', [
                        ['password123'],
                        ['A long passphrase mixing words, numbers and symbols', true],
                        ['Your date of birth'],
                        ['12345678'],
                    ]),
                    $this->trueFalse('Reusing the same password across multiple sites is a safe practice.', false),
                    $this->single('What does MFA stand for?', [
                        ['Multiple File Access'],
                        ['Multi-Factor Authentication', true],
                        ['Managed Firewall Access'],
                        ['Master Form Authentication'],
                    ]),
                    $this->single('A password manager helps you:', [
                        ['Share passwords publicly'],
                        ['Generate and store strong, unique passwords securely', true],
                        ['Disable passwords entirely'],
                        ['Email passwords to yourself'],
                    ]),
                    $this->single('Which is an example of a second authentication factor?', [
                        ['A longer password'],
                        ['A one-time code from an authenticator app', true],
                        ['Your username'],
                        ['Your email address'],
                    ]),
                    $this->trueFalse('Writing your password on a sticky note attached to your monitor is secure.', false),
                    $this->single('What is a "brute force" attack?', [
                        ['Physically breaking a computer'],
                        ['Systematically trying many passwords until one works', true],
                        ['Sending phishing emails'],
                        ['Encrypting files for ransom'],
                    ]),
                    $this->single('When should default vendor passwords be changed?', [
                        ['Never'],
                        ['Immediately upon setup', true],
                        ['After one year'],
                        ['Only if the device is hacked'],
                    ]),
                    $this->single('What makes a password harder to crack?', [
                        ['Greater length and complexity', true],
                        ['Using a common dictionary word'],
                        ['Using your own name'],
                        ['Using a short 4-digit PIN'],
                    ]),
                    $this->trueFalse('Enabling MFA significantly reduces the risk of account compromise.', true),
                ],
            ],
            [
                'title'       => 'Malware & Ransomware Defence',
                'description' => 'Understand viruses, worms, trojans, spyware and how to respond to ransomware.',
                'questions'   => [
                    $this->single('What is malware?', [
                        ['Malfunctioning hardware'],
                        ['Malicious software designed to harm or exploit systems', true],
                        ['A type of firewall'],
                        ['Mail-server software'],
                    ]),
                    $this->single('Ransomware typically:', [
                        ['Speeds up your computer'],
                        ['Encrypts your files and demands payment for decryption', true],
                        ['Backs up your data automatically'],
                        ['Updates your software'],
                    ]),
                    $this->trueFalse('Keeping regular offline backups helps you recover from ransomware.', true),
                    $this->single('A program that self-replicates to spread to other computers is a:', [
                        ['Worm', true],
                        ['Patch'],
                        ['Cookie'],
                        ['Firewall'],
                    ]),
                    $this->single('A "Trojan horse" is malware that:', [
                        ['Disguises itself as legitimate software', true],
                        ['Always shows a clear warning'],
                        ['Cannot be removed'],
                        ['Is purely hardware-based'],
                    ]),
                    $this->single('Which practice helps prevent malware infections?', [
                        ['Disabling antivirus'],
                        ['Keeping software updated and patched', true],
                        ['Opening every attachment'],
                        ['Running everything as administrator'],
                    ]),
                    $this->trueFalse('You should pay the ransom immediately to recover your files.', false),
                    $this->single('Spyware is designed to:', [
                        ['Secretly gather information about a user', true],
                        ['Improve system performance'],
                        ['Block advertisements only'],
                        ['Encrypt your backups'],
                    ]),
                    $this->single('What is a "zero-day" vulnerability?', [
                        ['A flaw unknown to the vendor, with no patch yet available', true],
                        ['A bug that only lasts zero days'],
                        ['A scheduled maintenance window'],
                        ['An expired certificate'],
                    ]),
                    $this->single('The best first response to a suspected malware infection is to:', [
                        ['Continue working as normal'],
                        ['Disconnect the device from the network and report it', true],
                        ['Shut down all company servers'],
                        ['Email the malware to IT'],
                    ]),
                ],
            ],
            [
                'title'       => 'Network Security Fundamentals',
                'description' => 'Firewalls, VPNs, secure protocols and defending against network-based attacks.',
                'questions'   => [
                    $this->single('What does a firewall do?', [
                        ['Cools down the server'],
                        ['Monitors and controls network traffic based on rules', true],
                        ['Stores passwords'],
                        ['Encrypts emails'],
                    ]),
                    $this->single('What does VPN stand for?', [
                        ['Virtual Private Network', true],
                        ['Verified Public Node'],
                        ['Virtual Public Name'],
                        ['Validated Personal Network'],
                    ]),
                    $this->trueFalse('Public Wi-Fi is always safe for banking transactions.', false),
                    $this->single('A VPN primarily provides:', [
                        ['Faster internet in all cases'],
                        ['An encrypted tunnel for your network traffic', true],
                        ['Free software downloads'],
                        ['More storage space'],
                    ]),
                    $this->single('Which protocol is encrypted?', [
                        ['HTTP'],
                        ['HTTPS', true],
                        ['FTP'],
                        ['Telnet'],
                    ]),
                    $this->single('What is a DDoS attack?', [
                        ['A data backup method'],
                        ['Overwhelming a service with traffic to make it unavailable', true],
                        ['A password reset process'],
                        ['A type of encryption'],
                    ]),
                    $this->single('Network segmentation helps by:', [
                        ['Increasing the attack surface'],
                        ['Limiting how far an attacker can move within a network', true],
                        ['Disabling all firewalls'],
                        ['Slowing down all traffic'],
                    ]),
                    $this->trueFalse('An Intrusion Detection System (IDS) monitors for suspicious activity.', true),
                    $this->single('A "default-deny" firewall policy means:', [
                        ['Allow everything by default'],
                        ['Block everything unless it is explicitly allowed', true],
                        ['Disable the firewall'],
                        ['Allow only HTTP'],
                    ]),
                    $this->single('Which port is commonly used by HTTPS?', [
                        ['21'],
                        ['80'],
                        ['443', true],
                        ['25'],
                    ]),
                ],
            ],
            [
                'title'       => 'Data Privacy & Protection',
                'description' => 'Handling personal data, classification, least privilege and breach reporting.',
                'questions'   => [
                    $this->single('What does PII stand for?', [
                        ['Public Internet Information'],
                        ['Personally Identifiable Information', true],
                        ['Private Internal Index'],
                        ['Protected Internet Identity'],
                    ]),
                    $this->trueFalse('Encrypting sensitive data at rest protects it if a device is stolen.', true),
                    $this->single('The principle of "least privilege" means:', [
                        ['Everyone is given administrator access'],
                        ['Users get only the access they need to do their job', true],
                        ['No one is allowed any access'],
                        ['Access is unlimited for convenience'],
                    ]),
                    $this->single('GDPR is primarily concerned with:', [
                        ['Network speed'],
                        ['The protection of personal data and privacy', true],
                        ['Software licensing'],
                        ['Hardware standards'],
                    ]),
                    $this->single('Data classification helps an organisation to:', [
                        ['Make all data public'],
                        ['Apply appropriate protection based on sensitivity', true],
                        ['Delete all of its data'],
                        ['Slow down its systems'],
                    ]),
                    $this->trueFalse('Sharing customer data without consent is acceptable if it is convenient.', false),
                    $this->single('What is data anonymisation?', [
                        ['Encrypting passwords'],
                        ['Removing identifiers so data cannot be traced to an individual', true],
                        ['Backing up data'],
                        ['Deleting all data permanently'],
                    ]),
                    $this->single('A data breach should be:', [
                        ['Ignored'],
                        ['Reported promptly per policy and regulations', true],
                        ['Hidden from authorities'],
                        ['Posted on social media'],
                    ]),
                    $this->single('Which of these is sensitive personal data?', [
                        ['A public company name'],
                        ['Health and medical records', true],
                        ['A headquarters office address'],
                        ['A public product catalogue'],
                    ]),
                    $this->trueFalse('Securely wiping or shredding old storage media helps prevent data leaks.', true),
                ],
            ],
            [
                'title'       => 'Secure Web Browsing & Email',
                'description' => 'Staying safe online: HTTPS, certificates, downloads, attachments and typosquatting.',
                'questions'   => [
                    $this->single('A padlock icon in the browser address bar indicates:', [
                        ['The site is owned by you'],
                        ['The connection is using HTTPS/TLS encryption', true],
                        ['The site has no advertisements'],
                        ['The site is government-run'],
                    ]),
                    $this->trueFalse('HTTPS alone guarantees a website is trustworthy and not malicious.', false),
                    $this->single('What are browser cookies?', [
                        ['Edible snacks'],
                        ['Small files that store site and session data', true],
                        ['A type of virus'],
                        ['Browser updates'],
                    ]),
                    $this->single('Before downloading software you should:', [
                        ['Download it from any pop-up'],
                        ['Verify the source is official and legitimate', true],
                        ['Disable your antivirus first'],
                        ['Trust every link you see'],
                    ]),
                    $this->single('An email attachment with a .exe extension from an unknown sender should be:', [
                        ['Opened immediately'],
                        ['Treated as suspicious and not opened', true],
                        ['Forwarded to friends'],
                        ['Renamed and then opened'],
                    ]),
                    $this->trueFalse('Keeping your browser updated helps protect against known vulnerabilities.', true),
                    $this->single('"Typosquatting" refers to:', [
                        ['Registering misspelled domain names to trick users', true],
                        ['A typing-tutor application'],
                        ['A secure login method'],
                        ['A built-in browser feature'],
                    ]),
                    $this->single('A safe practice when entering credentials online is to:', [
                        ['Check the URL and ensure it is the legitimate HTTPS site', true],
                        ['Enter them on any login form'],
                        ['Ignore certificate warnings'],
                        ['Use shared public computers'],
                    ]),
                    $this->single('A browser certificate warning usually means:', [
                        ['The site loads quickly'],
                        ['There may be a problem with the site\'s security or identity', true],
                        ['The site is ad-free'],
                        ['Your browser is broken'],
                    ]),
                    $this->trueFalse('You should ignore software update prompts to save time.', false),
                ],
            ],
            [
                'title'       => 'Mobile Device Security',
                'description' => 'Protecting smartphones and tablets: locks, app permissions, updates and lost devices.',
                'questions'   => [
                    $this->single('Why should you lock your phone with a PIN or biometric?', [
                        ['To save battery'],
                        ['To prevent unauthorised access to your data', true],
                        ['To increase storage'],
                        ['To improve the camera'],
                    ]),
                    $this->trueFalse('Installing apps only from official app stores reduces malware risk.', true),
                    $this->single('Jailbreaking or rooting a device:', [
                        ['Improves its security'],
                        ['Can remove built-in security protections', true],
                        ['Is always recommended'],
                        ['Has no security impact'],
                    ]),
                    $this->single('App permissions should be:', [
                        ['Granted fully to every app'],
                        ['Reviewed and limited to what is necessary', true],
                        ['Completely ignored'],
                        ['Always denied entirely'],
                    ]),
                    $this->single('If your work phone is lost, you should:', [
                        ['Wait a week before acting'],
                        ['Report it immediately so it can be wiped or secured', true],
                        ['Quietly buy a new one'],
                        ['Do nothing'],
                    ]),
                    $this->trueFalse('Connecting to any open public Wi-Fi without caution is safe for sensitive work.', false),
                    $this->single('Mobile device encryption protects:', [
                        ['Battery life'],
                        ['The data stored on the device', true],
                        ['Screen brightness'],
                        ['Network speed'],
                    ]),
                    $this->single('Keeping your mobile OS updated:', [
                        ['Only wastes data'],
                        ['Patches security vulnerabilities', true],
                        ['Always slows the phone down'],
                        ['Is unnecessary'],
                    ]),
                    $this->single('A suspicious SMS with a link asking you to verify your bank is likely:', [
                        ['A helpful reminder'],
                        ['Smishing (SMS phishing)', true],
                        ['A genuine system update'],
                        ['A trustworthy promotional offer'],
                    ]),
                    $this->trueFalse('Enabling remote wipe helps protect data on a lost device.', true),
                ],
            ],
            [
                'title'       => 'Cloud Security Basics',
                'description' => 'Shared responsibility, misconfiguration, access control and protecting cloud data.',
                'questions'   => [
                    $this->single('In the cloud "shared responsibility model", the customer is typically responsible for:', [
                        ['The physical data centre'],
                        ['Securing their own data, access and configurations', true],
                        ['The provider\'s hypervisor hardware'],
                        ['The provider\'s network cabling'],
                    ]),
                    $this->trueFalse('Misconfigured cloud storage buckets can accidentally expose data publicly.', true),
                    $this->single('A common cause of cloud data breaches is:', [
                        ['Strong encryption'],
                        ['Misconfiguration and weak access controls', true],
                        ['MFA being enabled'],
                        ['Regular patching'],
                    ]),
                    $this->single('To secure cloud accounts you should:', [
                        ['Disable MFA'],
                        ['Enable MFA and apply least privilege', true],
                        ['Share root credentials with everyone'],
                        ['Use one password everywhere'],
                    ]),
                    $this->single('Encryption of data in transit to the cloud typically uses:', [
                        ['Plain HTTP'],
                        ['TLS / HTTPS', true],
                        ['Telnet'],
                        ['No encryption'],
                    ]),
                    $this->trueFalse('You should regularly review who has access to your cloud resources.', true),
                    $this->single('A good practice for cloud API keys is to:', [
                        ['Hard-code them into public repositories'],
                        ['Store them securely and rotate them regularly', true],
                        ['Share them in chat channels'],
                        ['Email them to the team'],
                    ]),
                    $this->single('Logging and monitoring in the cloud helps to:', [
                        ['Only increase costs'],
                        ['Detect and investigate suspicious activity', true],
                        ['Slow services down'],
                        ['Hide breaches'],
                    ]),
                    $this->single('The root / primary admin cloud account should be:', [
                        ['Used daily for all routine tasks'],
                        ['Strongly protected and used only minimally', true],
                        ['Shared with the whole team'],
                        ['Left without MFA'],
                    ]),
                    $this->trueFalse('Backups of cloud data are unnecessary because the cloud never fails.', false),
                ],
            ],
            [
                'title'       => 'Incident Response & Reporting',
                'description' => 'How to detect, contain, report and learn from security incidents.',
                'questions'   => [
                    $this->single('What is the first thing to do when you suspect a security incident?', [
                        ['Ignore it'],
                        ['Report it to the security team according to policy', true],
                        ['Post about it online'],
                        ['Delete the logs'],
                    ]),
                    $this->trueFalse('Preserving evidence such as logs and screenshots is important during incident response.', true),
                    $this->single('The phases of incident response generally include:', [
                        ['Only deletion of files'],
                        ['Preparation, detection, containment, eradication and recovery', true],
                        ['Marketing and sales'],
                        ['Hardware replacement only'],
                    ]),
                    $this->single('"Containment" in incident response means:', [
                        ['Ignoring the threat'],
                        ['Limiting the spread and impact of the incident', true],
                        ['Paying the attackers'],
                        ['Shutting the business permanently'],
                    ]),
                    $this->single('Who should be notified during a major incident?', [
                        ['No one'],
                        ['The designated incident response team and management', true],
                        ['Only your friends'],
                        ['Competitors'],
                    ]),
                    $this->trueFalse('Reporting incidents quickly can reduce the overall damage.', true),
                    $this->single('A "post-incident review" (lessons learned) is used to:', [
                        ['Assign blame only'],
                        ['Improve processes and prevent recurrence', true],
                        ['Hide the incident'],
                        ['Increase future risk'],
                    ]),
                    $this->single('During an active breach you should follow:', [
                        ['Your own improvised steps'],
                        ['The organisation\'s incident response plan', true],
                        ['Random advice from social media'],
                        ['No process at all'],
                    ]),
                    $this->single('If you accidentally click a phishing link, you should:', [
                        ['Hide it and hope nothing happens'],
                        ['Report it immediately', true],
                        ['Secretly reformat your PC'],
                        ['Ignore it'],
                    ]),
                    $this->trueFalse('Documentation during an incident helps with recovery and compliance.', true),
                ],
            ],
            [
                'title'       => 'Cryptography & Encryption Basics',
                'description' => 'Encryption, hashing, keys, digital signatures and protecting data in transit and at rest.',
                'questions'   => [
                    $this->single('What is encryption?', [
                        ['Deleting data'],
                        ['Converting data into a coded form to prevent unauthorised access', true],
                        ['Compressing files'],
                        ['Backing up data'],
                    ]),
                    $this->single('Symmetric encryption uses:', [
                        ['Two different keys'],
                        ['The same key to encrypt and decrypt', true],
                        ['No key at all'],
                        ['A username only'],
                    ]),
                    $this->single('Asymmetric encryption uses:', [
                        ['One shared key'],
                        ['A public and private key pair', true],
                        ['No keys'],
                        ['A single PIN'],
                    ]),
                    $this->trueFalse('Hashing is a one-way function that cannot be easily reversed.', true),
                    $this->single('What is a digital signature used for?', [
                        ['Encrypting all network traffic'],
                        ['Verifying the authenticity and integrity of a message', true],
                        ['Speeding up networks'],
                        ['Storing passwords'],
                    ]),
                    $this->single('TLS is used to:', [
                        ['Slow down connections'],
                        ['Secure data in transit (for example, HTTPS)', true],
                        ['Delete cookies'],
                        ['Format disks'],
                    ]),
                    $this->trueFalse('Storing passwords as plain text is a secure practice.', false),
                    $this->single('Which is a strong, modern symmetric encryption algorithm?', [
                        ['MD5'],
                        ['AES', true],
                        ['ROT13'],
                        ['Base64'],
                    ]),
                    $this->single('Base64 is:', [
                        ['An encryption algorithm'],
                        ['An encoding scheme, not encryption', true],
                        ['A hashing algorithm'],
                        ['A firewall'],
                    ]),
                    $this->single('The purpose of "salting" a password hash is to:', [
                        ['Make it taste better'],
                        ['Defend against precomputed (rainbow table) attacks', true],
                        ['Speed up the hashing'],
                        ['Remove the hash'],
                    ]),
                ],
            ],
        ];
    }
}
