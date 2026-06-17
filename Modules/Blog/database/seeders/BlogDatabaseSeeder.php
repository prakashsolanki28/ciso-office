<?php

namespace Modules\Blog\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Tag;

class BlogDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::orderBy('id')->first() ?? User::factory()->create([
            'name'  => 'CISO Editorial',
            'email' => 'editorial@hrrl-ciso.test',
        ]);

        // ── Categories ───────────────────────────────────────────────
        $categories = collect([
            ['name' => 'Phishing & Email Security', 'color' => '#ef4444', 'description' => 'Spotting and stopping deceptive emails and messages.'],
            ['name' => 'Passwords & Authentication', 'color' => '#6366f1', 'description' => 'Credentials, MFA, and identity protection.'],
            ['name' => 'Malware & Ransomware',       'color' => '#f59e0b', 'description' => 'Malicious software, ransomware, and recovery.'],
            ['name' => 'Data Protection',            'color' => '#10b981', 'description' => 'Classifying, handling, and safeguarding data.'],
            ['name' => 'Security Awareness',         'color' => '#3b82f6', 'description' => 'Everyday habits that keep the enterprise safe.'],
        ])->mapWithKeys(fn ($c) => [
            $c['name'] => Category::firstOrCreate(['name' => $c['name']], $c)->id,
        ]);

        // ── Tags ─────────────────────────────────────────────────────
        $tags = collect([
            'Phishing', 'Email', 'MFA', 'Passwords', 'Ransomware', 'Backup',
            'Social Engineering', 'Data Classification', 'Compliance', 'Incident Response',
            'Mobile Security', 'VPN', 'Insider Threat', 'Awareness', 'Best Practices',
        ])->mapWithKeys(fn ($name) => [$name => Tag::firstOrCreate(['name' => $name])->id]);

        // ── Posts ────────────────────────────────────────────────────
        foreach ($this->posts() as $i => $post) {
            $blog = Blog::updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'user_id'          => $author->id,
                    'category_id'      => $categories[$post['category']] ?? null,
                    'title'            => $post['title'],
                    'excerpt'          => $post['excerpt'],
                    'content'          => $this->body($post),
                    'status'           => 'published',
                    'published_at'     => now()->subDays(3 * $i)->setTime(9, 0),
                    'meta_title'       => $post['title'],
                    'meta_description' => $post['excerpt'],
                ]
            );

            $blog->tags()->sync(
                collect($post['tags'])->map(fn ($t) => $tags[$t])->all()
            );
        }

        $this->command?->info('Seeded ' . count($this->posts()) . ' blog posts across ' . $categories->count() . ' categories.');
    }

    /** Assemble blog-content HTML from a structured post definition. */
    private function body(array $post): string
    {
        $html = "<p>{$post['lead']}</p>";

        if (! empty($post['quote'])) {
            $html .= "<blockquote>{$post['quote']}</blockquote>";
        }

        foreach ($post['sections'] as $section) {
            $html .= '<h2>' . e($section['heading']) . '</h2>';
            foreach ($section['paras'] ?? [] as $para) {
                $html .= "<p>{$para}</p>";
            }
            if (! empty($section['list'])) {
                $html .= '<ul>';
                foreach ($section['list'] as $item) {
                    $html .= "<li>{$item}</li>";
                }
                $html .= '</ul>';
            }
        }

        $html .= '<h2>Key takeaways</h2><ul>';
        foreach ($post['takeaways'] as $item) {
            $html .= "<li>{$item}</li>";
        }
        $html .= '</ul>';

        return $html;
    }

    private function posts(): array
    {
        return [
            [
                'title' => "How to Spot a Phishing Email Before You Click",
                'slug' => 'how-to-spot-a-phishing-email',
                'category' => 'Phishing & Email Security',
                'tags' => ['Phishing', 'Email', 'Awareness'],
                'excerpt' => 'Phishing is still the number one way attackers break in. Learn the tell-tale signs that separate a legitimate message from a trap.',
                'lead' => 'More than nine out of ten cyber attacks begin with a phishing email. The good news: most of them give themselves away if you know what to look for. A few seconds of scrutiny is often all it takes to avoid a costly mistake.',
                'sections' => [
                    [
                        'heading' => 'The red flags that matter most',
                        'paras' => ['Attackers rely on urgency and emotion to push you past your better judgement. Slow down whenever a message demands immediate action, and check these signals before you click anything:'],
                        'list' => [
                            '<strong>Mismatched sender address</strong> — the display name says "IT Helpdesk" but the real address is a random domain.',
                            '<strong>Generic greetings</strong> — "Dear user" instead of your name.',
                            '<strong>Unexpected attachments or links</strong> — especially invoices, shipping notices, or password-reset prompts you never requested.',
                            '<strong>Subtle misspellings</strong> in the domain, like <em>hrr1.com</em> instead of <em>hrrl.com</em>.',
                        ],
                    ],
                    [
                        'heading' => 'Hover before you click',
                        'paras' => ['Always hover over a link to preview the real destination. If the visible text and the actual URL disagree, treat it as hostile. On mobile, press and hold the link to reveal the same preview.'],
                    ],
                ],
                'quote' => 'When in doubt, throw it out — or better yet, report it. No legitimate request is ever ruined by a thirty-second verification.',
                'takeaways' => [
                    'Verify the sender\'s real address, not just the display name.',
                    'Never act on urgency alone — confirm through a trusted channel.',
                    'Report suspicious emails to the CISO Office instead of deleting them silently.',
                ],
            ],
            [
                'title' => "Why Multi-Factor Authentication Is Non-Negotiable",
                'slug' => 'why-mfa-is-non-negotiable',
                'category' => 'Passwords & Authentication',
                'tags' => ['MFA', 'Best Practices'],
                'excerpt' => 'A stolen password is only half a breach. Multi-factor authentication blocks the vast majority of account-takeover attempts — here is why it works.',
                'lead' => 'Passwords get leaked, guessed, and phished every day. Multi-factor authentication (MFA) adds a second, independent proof of identity so that a stolen password alone is no longer enough to get in.',
                'sections' => [
                    [
                        'heading' => 'The three factors',
                        'paras' => ['Strong authentication combines at least two different categories of proof:'],
                        'list' => [
                            'Something you <strong>know</strong> — a password or PIN.',
                            'Something you <strong>have</strong> — a phone app, hardware key, or token.',
                            'Something you <strong>are</strong> — a fingerprint or face scan.',
                        ],
                    ],
                    [
                        'heading' => 'Not all MFA is equal',
                        'paras' => [
                            'App-based codes and hardware security keys are far stronger than SMS, which can be intercepted or SIM-swapped. Where possible, prefer an authenticator app or a FIDO2 security key.',
                            'Beware of "MFA fatigue" attacks, where you are bombarded with approval prompts until you tap "approve" by mistake. If you receive a prompt you did not trigger, deny it and report it.',
                        ],
                    ],
                ],
                'takeaways' => [
                    'Enable MFA on every account that offers it — especially email.',
                    'Choose an authenticator app or hardware key over SMS.',
                    'Never approve a login request you did not start.',
                ],
            ],
            [
                'title' => "Building Strong Passphrases You'll Actually Remember",
                'slug' => 'building-strong-passphrases',
                'category' => 'Passwords & Authentication',
                'tags' => ['Passwords', 'Best Practices'],
                'excerpt' => 'Length beats complexity. Discover how a simple four-word passphrase outperforms cryptic passwords no one can remember.',
                'lead' => 'The old advice — mix upper, lower, numbers, and symbols — produced passwords that were hard for humans and easy for computers. Modern guidance flips that: make it long, make it unique, and let a manager remember it for you.',
                'sections' => [
                    [
                        'heading' => 'Why length wins',
                        'paras' => ['Each extra character multiplies the time needed to crack a password. A random four-word passphrase like "amber-harbor-piano-cloud" is both easy to recall and astronomically hard to brute-force.'],
                    ],
                    [
                        'heading' => 'Let a password manager do the work',
                        'paras' => ['You cannot reuse what you never have to memorise. A password manager generates and stores a unique credential for every site, so a breach of one service can never cascade into another.'],
                        'list' => [
                            'Use a unique password for every account.',
                            'Protect the manager itself with a strong master passphrase and MFA.',
                            'Never store passwords in spreadsheets, notes, or browsers without protection.',
                        ],
                    ],
                ],
                'takeaways' => [
                    'Prefer long passphrases over short, complex strings.',
                    'Never reuse a password across services.',
                    'Adopt a password manager to make uniqueness effortless.',
                ],
            ],
            [
                'title' => "Ransomware: How It Spreads and How to Stop It",
                'slug' => 'ransomware-how-it-spreads',
                'category' => 'Malware & Ransomware',
                'tags' => ['Ransomware', 'Backup'],
                'excerpt' => 'Ransomware can lock an entire organisation in minutes. Understand the attack chain and the defences that actually break it.',
                'lead' => 'Ransomware encrypts your files and demands payment for their release. By the time the ransom note appears, the damage is done — which is why prevention and recovery planning matter far more than negotiation.',
                'sections' => [
                    [
                        'heading' => 'The typical attack chain',
                        'paras' => ['Most ransomware follows a predictable path, and each step is an opportunity to stop it:'],
                        'list' => [
                            '<strong>Entry</strong> via a phishing email or exposed remote service.',
                            '<strong>Escalation</strong> as attackers steal credentials and move laterally.',
                            '<strong>Exfiltration</strong> of sensitive data for double extortion.',
                            '<strong>Encryption</strong> of files across the network.',
                        ],
                    ],
                    [
                        'heading' => 'The 3-2-1 backup rule',
                        'paras' => ['Reliable, offline backups are the single most effective ransomware defence. Keep <strong>three</strong> copies of your data, on <strong>two</strong> different media, with <strong>one</strong> stored offline or immutable. Test your restores regularly — an untested backup is only a hope.'],
                    ],
                ],
                'quote' => 'Paying the ransom funds the next attack and offers no guarantee of recovery. A tested backup is the only ransom note you want to read.',
                'takeaways' => [
                    'Patch internet-facing services and disable unused remote access.',
                    'Maintain offline, immutable backups and test restores.',
                    'Report the first signs of compromise immediately — speed limits the blast radius.',
                ],
            ],
            [
                'title' => "Recognising Social Engineering Tactics at Work",
                'slug' => 'recognising-social-engineering',
                'category' => 'Security Awareness',
                'tags' => ['Social Engineering', 'Awareness'],
                'excerpt' => 'Attackers hack people, not just computers. Learn the psychological tricks behind social engineering and how to shut them down.',
                'lead' => 'Social engineering is the art of manipulating people into breaking security rules. It works because it targets human instincts — trust, helpfulness, and fear — rather than technical flaws.',
                'sections' => [
                    [
                        'heading' => 'Common pretexts',
                        'paras' => ['The story changes but the goal is always the same: get you to hand over access or information. Watch for:'],
                        'list' => [
                            'A "manager" demanding an urgent payment or gift cards.',
                            'A "vendor" needing your login to "fix" an issue.',
                            'A friendly stranger tailgating through a secure door.',
                            'A caller who already knows just enough about you to sound credible.',
                        ],
                    ],
                    [
                        'heading' => 'The pause that protects',
                        'paras' => ['Manufactured urgency is the common thread. Whenever a request pressures you to bypass normal process, stop and verify through an independent, known channel — never the contact details the requester provides.'],
                    ],
                ],
                'takeaways' => [
                    'Authority and urgency are manipulation tools — slow down.',
                    'Verify unusual requests through a separate, trusted channel.',
                    'It is always acceptable to say "let me confirm and call you back".',
                ],
            ],
            [
                'title' => "A Practical Guide to Data Classification",
                'slug' => 'practical-guide-to-data-classification',
                'category' => 'Data Protection',
                'tags' => ['Data Classification', 'Compliance'],
                'excerpt' => 'You cannot protect what you have not labelled. A simple classification scheme tells everyone how to handle each piece of information.',
                'lead' => 'Data classification assigns a sensitivity level to information so that handling, sharing, and storage rules are clear to everyone. It turns vague intentions into consistent, auditable behaviour.',
                'sections' => [
                    [
                        'heading' => 'A four-tier model',
                        'paras' => ['Most organisations use four straightforward levels:'],
                        'list' => [
                            '<strong>Public</strong> — approved for open release.',
                            '<strong>Internal</strong> — for employees, low impact if exposed.',
                            '<strong>Confidential</strong> — sensitive business or personal data.',
                            '<strong>Restricted</strong> — severe impact if exposed; strict access controls.',
                        ],
                    ],
                    [
                        'heading' => 'Handling follows the label',
                        'paras' => ['Once data is labelled, the rules write themselves: encryption for confidential and restricted data, no external sharing without approval, and secure disposal when it is no longer needed.'],
                    ],
                ],
                'takeaways' => [
                    'Label data at creation, not as an afterthought.',
                    'Match controls — encryption, access, retention — to the classification.',
                    'When unsure of a level, treat the data as confidential until confirmed.',
                ],
            ],
            [
                'title' => "What to Do in the First 10 Minutes of a Breach",
                'slug' => 'first-10-minutes-of-a-breach',
                'category' => 'Security Awareness',
                'tags' => ['Incident Response', 'Best Practices'],
                'excerpt' => 'The first response to a security incident shapes everything that follows. Here is a calm, practical playbook for the critical first minutes.',
                'lead' => 'Discovering a possible breach is stressful, but your first actions matter enormously. Acting calmly — and avoiding a few common mistakes — can be the difference between a contained event and a full-blown crisis.',
                'sections' => [
                    [
                        'heading' => 'Do this immediately',
                        'list' => [
                            'Stay calm and do not try to hide the incident.',
                            'Disconnect the affected device from the network — unplug the cable or turn off Wi-Fi.',
                            'Do <strong>not</strong> delete files or power down; preserve evidence.',
                            'Report to the CISO hotline from a different device.',
                            'Write down what you saw — error messages, times, and actions.',
                        ],
                    ],
                    [
                        'heading' => 'Why these steps work',
                        'paras' => ['Isolating the device stops the spread, while preserving its state lets responders understand what happened. Early, honest reporting buys the response team the most valuable resource of all: time.'],
                    ],
                ],
                'takeaways' => [
                    'Isolate, do not erase — preserve the evidence.',
                    'Report early through a trusted, separate channel.',
                    'Document what you observed while it is fresh.',
                ],
            ],
            [
                'title' => "Securing Your Mobile Devices On and Off the Network",
                'slug' => 'securing-your-mobile-devices',
                'category' => 'Data Protection',
                'tags' => ['Mobile Security', 'VPN'],
                'excerpt' => 'Phones and laptops carry the keys to the kingdom. A handful of habits keeps your mobile life secure wherever you work.',
                'lead' => 'Mobile devices blur the line between work and personal life, and that makes them a favourite target. Treat every device that touches company data with the same care you give your workstation.',
                'sections' => [
                    [
                        'heading' => 'The essentials',
                        'list' => [
                            'Lock every device with a strong PIN or biometrics and enable auto-lock.',
                            'Keep the operating system and apps fully updated.',
                            'Enable remote wipe so a lost device can be erased.',
                            'Install apps only from official stores.',
                        ],
                    ],
                    [
                        'heading' => 'Beware of public Wi-Fi',
                        'paras' => ['Open networks in cafés, airports, and hotels can be monitored or spoofed. Use the corporate VPN whenever you connect from outside the office, and never access sensitive systems over an untrusted network without it.'],
                    ],
                ],
                'takeaways' => [
                    'Lock, update, and enable remote wipe on every device.',
                    'Always use the VPN on untrusted networks.',
                    'Report lost or stolen devices immediately.',
                ],
            ],
            [
                'title' => "The Insider Threat: When the Risk Comes From Within",
                'slug' => 'the-insider-threat',
                'category' => 'Security Awareness',
                'tags' => ['Insider Threat', 'Awareness'],
                'excerpt' => 'Not every threat is an outsider. Understand the accidental and malicious insider, and the culture that keeps both in check.',
                'lead' => 'Insiders already have access, context, and trust — which makes their mistakes, and occasionally their malice, especially damaging. Most insider incidents are accidental, and almost all are preventable.',
                'sections' => [
                    [
                        'heading' => 'Two kinds of insider risk',
                        'paras' => ['Insider threats fall into two broad camps:'],
                        'list' => [
                            '<strong>Accidental</strong> — sending data to the wrong recipient, misconfiguring a share, or falling for a phish.',
                            '<strong>Malicious</strong> — deliberately stealing data or sabotaging systems, often around a resignation or grievance.',
                        ],
                    ],
                    [
                        'heading' => 'Least privilege and speaking up',
                        'paras' => ['Granting people only the access they need limits the damage any single account can do. Just as important is a culture where colleagues feel safe reporting concerns early — most serious incidents had warning signs that someone noticed.'],
                    ],
                ],
                'takeaways' => [
                    'Most insider incidents are honest mistakes — design processes to catch them.',
                    'Apply least privilege so access matches the role.',
                    'If something feels off, report it; early signals prevent later harm.',
                ],
            ],
            [
                'title' => "Safe Browsing Habits for the Modern Workplace",
                'slug' => 'safe-browsing-habits',
                'category' => 'Phishing & Email Security',
                'tags' => ['Best Practices', 'Awareness'],
                'excerpt' => 'The browser is where most of our work — and most web-based attacks — happen. Build habits that keep everyday browsing safe.',
                'lead' => 'The web browser is the modern workplace\'s front door, and attackers know it. Malicious ads, fake downloads, and lookalike sites all aim to catch a distracted moment. A few steady habits close most of those doors.',
                'sections' => [
                    [
                        'heading' => 'Check the address, not the appearance',
                        'paras' => ['Attackers clone login pages perfectly, so the look of a site proves nothing. Confirm the exact domain in the address bar before entering credentials, and be suspicious of links that arrive unexpectedly.'],
                    ],
                    [
                        'heading' => 'Everyday safe habits',
                        'list' => [
                            'Keep your browser and extensions updated; remove ones you do not use.',
                            'Download software only from official, known sources.',
                            'Do not dismiss certificate or security warnings — investigate them.',
                            'Sign out of sensitive sites on shared or public computers.',
                        ],
                    ],
                ],
                'takeaways' => [
                    'Verify the domain before you log in anywhere.',
                    'Keep the browser lean and updated.',
                    'Treat security warnings as signals, not nuisances.',
                ],
            ],
        ];
    }
}
