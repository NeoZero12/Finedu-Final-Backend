<?php

namespace App\Support;

class ModuleJournalSources
{
    public static function for(string $moduleTitle): array
    {
        return self::definitions()[$moduleTitle] ?? [];
    }

    public static function all(): array
    {
        $all = [];

        foreach (self::definitions() as $sources) {
            foreach ($sources as $source) {
                $all[$source['url']] = $source;
            }
        }

        return array_values($all);
    }

    public static function definitions(): array
    {
        return array (
  'Dasar Investasi untuk Mahasiswa' => 
  array (
    0 => 
    array (
      'judul' => 'How financial literacy moderate the association between behaviour biases and investment decision?',
      'penulis' => 'Mohd Adil dkk.',
      'jurnal' => 'Asian Journal of Accounting Research',
      'url' => 'https://doi.org/10.1108/AJAR-09-2020-0086',
    ),
    1 => 
    array (
      'judul' => 'THE INFLUENCE OF FINANCIAL LITERACY, FINANCIAL BEHAVIOR AND INCOME ON INVESTMENT DECISION',
      'penulis' => 'Baiq Fitri Arianti',
      'jurnal' => 'EAJ (Economic and Accounting Journal)',
      'url' => 'https://doi.org/10.32493/eaj.v1i1.y2018.p1-10',
    ),
    2 => 
    array (
      'judul' => 'The Economic Importance of Financial Literacy: Theory and Evidence',
      'penulis' => 'Annamaria Lusardi, Olivia S. Mitchell',
      'jurnal' => 'Journal of Economic Literature',
      'url' => 'https://doi.org/10.1257/jel.52.1.5',
    ),
    3 => 
    array (
      'judul' => 'The roles of financial literacy and overconfidence in investment decisions in Saudi Arabia',
      'penulis' => 'Abdullah Hamoud Ali Seraj dkk.',
      'jurnal' => 'Frontiers in Psychology',
      'url' => 'https://doi.org/10.3389/fpsyg.2022.1005075',
    ),
    4 => 
    array (
      'judul' => 'Impact of financial literacy, mental budgeting and self control on financial wellbeing: Mediating impact of investment decision making',
      'penulis' => 'Ruofan Bai',
      'jurnal' => 'PLOS ONE',
      'url' => 'https://doi.org/10.1371/journal.pone.0294466',
    ),
    5 => 
    array (
      'judul' => 'Heuristic biases and investment decisions: multiple mediation mechanisms of risk tolerance and financial literacy—a survey at the Tanzania stock market',
      'penulis' => 'Pendo Kasoga',
      'jurnal' => 'Journal of Money and Business',
      'url' => 'https://doi.org/10.1108/JMB-10-2021-0037',
    ),
    6 => 
    array (
      'judul' => 'Influence of Financial Literacy and Risk Perception on Choice of Investment',
      'penulis' => 'Selim Aren, Asiye Nur Zengin',
      'jurnal' => 'Procedia - Social and Behavioral Sciences',
      'url' => 'https://doi.org/10.1016/j.sbspro.2016.11.047',
    ),
    7 => 
    array (
      'judul' => 'Is Financial Literacy Associated with Investment in Financial Markets in the United States?',
      'penulis' => 'Mostafa Saidur Rahim Khan dkk.',
      'jurnal' => 'Sustainability',
      'url' => 'https://doi.org/10.3390/su12187370',
    ),
    8 => 
    array (
      'judul' => 'Financially Savvy or Swayed by Biases? The Impact of Financial Literacy on Investment Decisions: A Study on Indian Retail Investors',
      'penulis' => 'Abhilasha Agarwal dkk.',
      'jurnal' => 'Journal of Risk and Financial Management',
      'url' => 'https://doi.org/10.3390/jrfm18060322',
    ),
    9 => 
    array (
      'judul' => 'Does Hyperbolic Discounting Mediate the Association Between Financial Literacy and Investment in Risky Assets?',
      'penulis' => 'Mostafa Saidur Rahim Khan, Yoshihiko Kadoya',
      'jurnal' => 'International Journal of Financial Studies',
      'url' => 'https://doi.org/10.3390/ijfs14030072',
    ),
  ),
  'Memahami Inflasi dan Daya Beli' => 
  array (
    0 => 
    array (
      'judul' => 'Inflation and Risky Investments',
      'penulis' => 'Hannu Laurila, Jukka Ilomäki',
      'jurnal' => 'Journal of Risk and Financial Management',
      'url' => 'https://doi.org/10.3390/jrfm13120329',
    ),
    1 => 
    array (
      'judul' => 'Inflation expectations and consumer spending: the role of household balance sheets',
      'penulis' => 'Lenard Lieb, Johannes Schuffels',
      'jurnal' => 'Empirical Economics',
      'url' => 'https://doi.org/10.1007/s00181-022-02222-8',
    ),
    2 => 
    array (
      'judul' => 'Inflation Expectations and Readiness to Spend: Cross-Sectional Evidence',
      'penulis' => 'Rüdiger Bachmann dkk.',
      'jurnal' => 'American Economic Journal: Economic Policy',
      'url' => 'https://doi.org/10.1257/pol.20130292',
    ),
    3 => 
    array (
      'judul' => 'Inflation expectations and household expenditure: Evidence from pseudo-panel data in Japan',
      'penulis' => 'Takeshi Niizeki, Masahiro Hori',
      'jurnal' => 'Journal of Economic Behavior &amp; Organization',
      'url' => 'https://doi.org/10.1016/j.jebo.2023.08.008',
    ),
    4 => 
    array (
      'judul' => 'What matters in households’ inflation expectations?',
      'penulis' => 'Philippe Andrade dkk.',
      'jurnal' => 'Journal of Monetary Economics',
      'url' => 'https://doi.org/10.1016/j.jmoneco.2023.05.007',
    ),
    5 => 
    array (
      'judul' => 'Expectations of inflation, wages and spending: Evidence from a consumer survey',
      'penulis' => 'Monica Jain dkk.',
      'jurnal' => 'Economics Letters',
      'url' => 'https://doi.org/10.1016/j.econlet.2025.112629',
    ),
    6 => 
    array (
      'judul' => 'What determines households inflation expectations? Theory and evidence from a household survey',
      'penulis' => 'Joshy Easaw dkk.',
      'jurnal' => 'European Economic Review',
      'url' => 'https://doi.org/10.1016/j.euroecorev.2013.02.009',
    ),
    7 => 
    array (
      'judul' => 'Public debt and household inflation expectations',
      'penulis' => 'Francesco Grigoli, Damiano Sandri',
      'jurnal' => 'Journal of International Economics',
      'url' => 'https://doi.org/10.1016/j.jinteco.2024.104003',
    ),
    8 => 
    array (
      'judul' => 'Peer influence and inflation expectations: Evidence from households’ social comparisons',
      'penulis' => 'Taniya Ghosh, Abhishek Gorsi',
      'jurnal' => 'Economic Modelling',
      'url' => 'https://doi.org/10.1016/j.econmod.2025.107202',
    ),
    9 => 
    array (
      'judul' => 'Household Inflation Expectations and Consumer Spending: Evidence from Panel Data',
      'penulis' => 'Mary A. Burke, Ali Ozdagli',
      'jurnal' => 'Review of Economics and Statistics',
      'url' => 'https://doi.org/10.1162/rest_a_01118',
    ),
  ),
  'Dana Darurat dan Ketahanan Finansial' => 
  array (
    0 => 
    array (
      'judul' => 'The Effect of Student Loan Debt on Emergency Savings and the Moderating Role of Financial Knowledge: Evidence from the U.S. Survey of Household Economics and Decisionmaking',
      'penulis' => 'Thomas Korankye dkk.',
      'jurnal' => 'Journal of Risk and Financial Management',
      'url' => 'https://doi.org/10.3390/jrfm17090420',
    ),
    1 => 
    array (
      'judul' => 'From Intention to Adequate Emergency Fund Savings through Fintech Use: Evidence from a Survey Study',
      'penulis' => 'Ying Chen dkk.',
      'jurnal' => 'Financial Services Review',
      'url' => 'https://doi.org/10.61190/fsr.v33i3.4050',
    ),
    2 => 
    array (
      'judul' => 'Precautionary Savings Against Health Risks',
      'penulis' => 'Tansel Yilmazer, Robert L. Scharff',
      'jurnal' => 'Research on Aging',
      'url' => 'https://doi.org/10.1177/0164027512473487',
    ),
    3 => 
    array (
      'judul' => 'Financial literacy and household financial resilience',
      'penulis' => 'Taixing Liu dkk.',
      'jurnal' => 'Finance Research Letters',
      'url' => 'https://doi.org/10.1016/j.frl.2024.105378',
    ),
    4 => 
    array (
      'judul' => 'Determinants of financial resilience: insights from an emerging economy',
      'penulis' => 'Fazelina Sahul Hamid dkk.',
      'jurnal' => 'Journal of Social and Economic Development',
      'url' => 'https://doi.org/10.1007/s40847-023-00239-y',
    ),
    5 => 
    array (
      'judul' => 'Digital inclusive finance and the resilience of households involved in financial markets',
      'penulis' => 'Geng Peng, Fang Liu',
      'jurnal' => 'Finance Research Letters',
      'url' => 'https://doi.org/10.1016/j.frl.2024.106288',
    ),
    6 => 
    array (
      'judul' => 'Liquidity constraints and precautionary saving',
      'penulis' => 'Christopher D. Carroll dkk.',
      'jurnal' => 'Journal of Economic Theory',
      'url' => 'https://doi.org/10.1016/j.jet.2021.105276',
    ),
    7 => 
    array (
      'judul' => 'Required or voluntary financial education and saving behaviors',
      'penulis' => 'William B. Walstad, Jamie Wagner',
      'jurnal' => 'The Journal of Economic Education',
      'url' => 'https://doi.org/10.1080/00220485.2022.2144573',
    ),
    8 => 
    array (
      'judul' => 'The Interplay of Financial Safety Nets, Long-Term Goals, and Saving Habits: A Moderated Mediation Study',
      'penulis' => 'Congrong Ouyang dkk.',
      'jurnal' => 'International Journal of Financial Studies',
      'url' => 'https://doi.org/10.3390/ijfs13010047',
    ),
    9 => 
    array (
      'judul' => 'Financial Wellbeing and Financial Resilience: Insights from Personal Experiences and Gender Differences',
      'penulis' => 'Arturo Garcia-Santillan dkk.',
      'jurnal' => 'Journal of Risk and Financial Management',
      'url' => 'https://doi.org/10.3390/jrfm19030217',
    ),
  ),
  'Keamanan Digital dalam Transaksi Keuangan' => 
  array (
    0 => 
    array (
      'judul' => 'The Role of Consumers’ Perceived Security, Perceived Control, Interface Design Features, and Conscientiousness in Continuous Use of Mobile Payment Services',
      'penulis' => 'Jiaxin Zhang dkk.',
      'jurnal' => 'Sustainability',
      'url' => 'https://doi.org/10.3390/su11236843',
    ),
    1 => 
    array (
      'judul' => 'Customer adoption of p2p mobile payment systems: The role of perceived risk',
      'penulis' => 'Daniel Belanche dkk.',
      'jurnal' => 'Telematics and Informatics',
      'url' => 'https://doi.org/10.1016/j.tele.2022.101851',
    ),
    2 => 
    array (
      'judul' => 'The effect of mobile-wallet service dimensions on customer satisfaction and loyalty: An empirical study',
      'penulis' => 'Ahmed S. Ajina dkk.',
      'jurnal' => 'Cogent Business &amp; Management',
      'url' => 'https://doi.org/10.1080/23311975.2023.2229544',
    ),
    3 => 
    array (
      'judul' => 'Is the Convenience Worth the Risk? An Investigation of Mobile Payment Usage',
      'penulis' => 'Abhipsa Pal dkk.',
      'jurnal' => 'Information Systems Frontiers',
      'url' => 'https://doi.org/10.1007/s10796-020-10070-z',
    ),
    4 => 
    array (
      'judul' => 'Exploring consumer perceived risk and trust for online payments: An empirical study in China’s younger generation',
      'penulis' => 'Qing Yang dkk.',
      'jurnal' => 'Computers in Human Behavior',
      'url' => 'https://doi.org/10.1016/j.chb.2015.03.058',
    ),
    5 => 
    array (
      'judul' => 'Digital literacy, online security behaviors and E-payment intention',
      'penulis' => 'Thu Thuy Nguyen dkk.',
      'jurnal' => 'Journal of Open Innovation: Technology, Market, and Complexity',
      'url' => 'https://doi.org/10.1016/j.joitmc.2024.100292',
    ),
    6 => 
    array (
      'judul' => 'How AI-enabled security features shape payment security perceptions and trust in digital payment systems',
      'penulis' => 'Kingsley Ofosu-Ampong dkk.',
      'jurnal' => 'Telematics and Informatics Reports',
      'url' => 'https://doi.org/10.1016/j.teler.2026.100298',
    ),
    7 => 
    array (
      'judul' => 'Consumer Trust in Digital Payment Systems',
      'penulis' => 'Taghreed Beheri',
      'jurnal' => 'Journal of Information Systems Engineering and Management',
      'url' => 'https://doi.org/10.52783/jisem.v10i42s.8179',
    ),
    8 => 
    array (
      'judul' => 'Analysis of Hybrid Securing Digital Payment System through Risk Perception',
      'penulis' => 'C. Vijesh Joe',
      'jurnal' => 'Journal of Electronics and Informatics',
      'url' => 'https://doi.org/10.36548/jei.2022.4.001',
    ),
    9 => 
    array (
      'judul' => 'A survey of trust and reputation systems for online service provision',
      'penulis' => 'Audun Jøsang dkk.',
      'jurnal' => 'Decision Support Systems',
      'url' => 'https://doi.org/10.1016/j.dss.2005.05.019',
    ),
  ),
  'E-Wallet, QRIS, dan Kebiasaan Aman' => 
  array (
    0 => 
    array (
      'judul' => 'Moderating Role of Perceived Trust and Perceived Service Quality on Consumers’ Use Behavior of Alipay e-wallet System: The Perspectives of Technology Acceptance Model and Theory of Planned Behavior',
      'penulis' => 'Yang Tian dkk.',
      'jurnal' => 'Human Behavior and Emerging Technologies',
      'url' => 'https://doi.org/10.1155/2023/5276406',
    ),
    1 => 
    array (
      'judul' => 'E-Wallet Adoption in Digital Payment Services: The Impact of Convenience, Trust, and Lifestyle',
      'penulis' => 'Zuni Maulidiya, Khusnudin Khusnudin',
      'jurnal' => 'Formosa Journal of Multidisciplinary Research',
      'url' => 'https://doi.org/10.55927/fjmr.v4i4.137',
    ),
    2 => 
    array (
      'judul' => 'Adoption of e-wallets: trust and perceived risk in Generation Z in Colombia',
      'penulis' => 'Catalina Gómez-Hurtado dkk.',
      'jurnal' => 'Spanish Journal of Marketing - ESIC',
      'url' => 'https://doi.org/10.1108/SJME-01-2024-0017',
    ),
    3 => 
    array (
      'judul' => 'Perceived Trust, Convenience and Promotion For the Adoption of e-Wallet',
      'penulis' => 'Jiet Ping Kiew dkk.',
      'jurnal' => 'International Journal of Academic Research in Business and Social Sciences',
      'url' => 'https://doi.org/10.6007/IJARBSS/v12-I9/14591',
    ),
    4 => 
    array (
      'judul' => 'FINTECH PAYMENT ADOPTION AMONG MICRO-ENTERPRISES: THE ROLE OF PERCEIVED RISK AND TRUST',
      'penulis' => 'Anissa Hakim Purwantini, Friztina Anisa',
      'jurnal' => 'Jurnal ASET (Akuntansi Riset)',
      'url' => 'https://doi.org/10.17509/jaset.v13i2.37212',
    ),
    5 => 
    array (
      'judul' => 'E-WOM and Adoption E-Wallet: The Role of Trust as a Mediating Variable',
      'penulis' => 'Rilo Fajar Maritha, Rini Kuswati',
      'jurnal' => 'Advances in Economics, Business and Management Research',
      'url' => 'https://doi.org/10.2991/aebmr.k.220602.024',
    ),
    6 => 
    array (
      'judul' => 'Effect of Consumer Trust and Perceived Risk on e-Wallet Adoption: Consideration for Technology Startup Entrepreneurs',
      'penulis' => 'Melisa Krisnawati dkk.',
      'jurnal' => 'Jurnal Entrepreneur dan Entrepreneurship',
      'url' => 'https://doi.org/10.37715/jee.v10i2.2212',
    ),
    7 => 
    array (
      'judul' => 'Exploring the Impact of Trust, Perceived Utility, Ease of Use Perception, and Social Influence on E-Payment Adoption',
      'penulis' => 'Lissa Rosdiana Noer dkk.',
      'jurnal' => 'Jurnal Sosial Humaniora',
      'url' => 'https://doi.org/10.12962/j24433527.v16i1.18667',
    ),
    8 => 
    array (
      'judul' => 'QRIS in Indonesia: A Comprehensive Literature Review on Adoption, Challenges, and Opportunities',
      'penulis' => 'Dian Prawitasari dkk.',
      'jurnal' => 'Revenue: Jurnal Manajemen Bisnis Islam',
      'url' => 'https://doi.org/10.24042/revenue.v5i1.22760',
    ),
    9 => 
    array (
      'judul' => 'Revolutionizing Payment Systems: The Integration of TRAM and Trust in QRIS Adoption for Micro, Small, and Medium Enterprises in Indonesia',
      'penulis' => 'Adisthy Shabrina Nurqamarani dkk.',
      'jurnal' => 'Journal of Information Systems Engineering and Business Intelligence',
      'url' => 'https://doi.org/10.20473/jisebi.10.3.314-327',
    ),
  ),
  'Anggaran Bulanan dan Prioritas Pengeluaran' => 
  array (
    0 => 
    array (
      'judul' => 'Does Financial Literacy Affect Household Financial Behavior? The Role of Limited Attention',
      'penulis' => 'Shulin Xu dkk.',
      'jurnal' => 'Frontiers in Psychology',
      'url' => 'https://doi.org/10.3389/fpsyg.2022.906153',
    ),
    1 => 
    array (
      'judul' => 'Household behavior in practicing mental budgeting based on the theory of planned behavior',
      'penulis' => 'Ume Habibah dkk.',
      'jurnal' => 'Financial Innovation',
      'url' => 'https://doi.org/10.1186/s40854-018-0108-y',
    ),
    2 => 
    array (
      'judul' => 'The Interplay of Mental Budgeting, Self-Control, and Financial Behavior: Implications for Individual Financial Well-Being',
      'penulis' => 'Syed Atif Ali dkk.',
      'jurnal' => 'Pakistan Journal of Humanities and Social Sciences',
      'url' => 'https://doi.org/10.52131/pjhss.2024.v12i2.2102',
    ),
    3 => 
    array (
      'judul' => 'Impact of financial literacy, mental budgeting and self control on financial wellbeing: Mediating impact of investment decision making',
      'penulis' => 'Ruofan Bai',
      'jurnal' => 'PLOS ONE',
      'url' => 'https://doi.org/10.1371/journal.pone.0294466',
    ),
    4 => 
    array (
      'judul' => 'Examining the Impact of Financial Literacy, Financial Self-Control, and Demographic Determinants on Individual Financial Performance and Behavior: An Insight from the Lebanese Crisis Period',
      'penulis' => 'Jeanne Laure Mawad dkk.',
      'jurnal' => 'Sustainability',
      'url' => 'https://doi.org/10.3390/su142215129',
    ),
    5 => 
    array (
      'judul' => 'Effects of limited attention on investors\' trading behavior: Evidence from online ranking data',
      'penulis' => 'Sujung Choi, Woon Youl Choi',
      'jurnal' => 'Pacific-Basin Finance Journal',
      'url' => 'https://doi.org/10.1016/j.pacfin.2019.06.007',
    ),
    6 => 
    array (
      'judul' => 'Limited Attention as a Scarce Resource in Information‐Rich Economies',
      'penulis' => 'Josef Falkinger',
      'jurnal' => 'The Economic Journal',
      'url' => 'https://doi.org/10.1111/j.1468-0297.2008.02182.x',
    ),
    7 => 
    array (
      'judul' => 'Limited attention, information disclosure, and financial reporting',
      'penulis' => 'David Hirshleifer, Siew Hong Teoh',
      'jurnal' => 'Journal of Accounting and Economics',
      'url' => 'https://doi.org/10.1016/j.jacceco.2003.10.002',
    ),
    8 => 
    array (
      'judul' => 'The role and relevance of domain knowledge, perceptions of planning importance, and risk tolerance in predicting savings intentions',
      'penulis' => 'Gerry Croy dkk.',
      'jurnal' => 'Journal of Economic Psychology',
      'url' => 'https://doi.org/10.1016/j.joep.2010.06.002',
    ),
    9 => 
    array (
      'judul' => 'Required or voluntary financial education and saving behaviors',
      'penulis' => 'William B. Walstad, Jamie Wagner',
      'jurnal' => 'The Journal of Economic Education',
      'url' => 'https://doi.org/10.1080/00220485.2022.2144573',
    ),
  ),
  'Menabung Konsisten dan Bunga Majemuk' => 
  array (
    0 => 
    array (
      'judul' => 'The Interplay of Financial Safety Nets, Long-Term Goals, and Saving Habits: A Moderated Mediation Study',
      'penulis' => 'Congrong Ouyang dkk.',
      'jurnal' => 'International Journal of Financial Studies',
      'url' => 'https://doi.org/10.3390/ijfs13010047',
    ),
    1 => 
    array (
      'judul' => 'Required or voluntary financial education and saving behaviors',
      'penulis' => 'William B. Walstad, Jamie Wagner',
      'jurnal' => 'The Journal of Economic Education',
      'url' => 'https://doi.org/10.1080/00220485.2022.2144573',
    ),
    2 => 
    array (
      'judul' => 'From Intention to Adequate Emergency Fund Savings through Fintech Use: Evidence from a Survey Study',
      'penulis' => 'Ying Chen dkk.',
      'jurnal' => 'Financial Services Review',
      'url' => 'https://doi.org/10.61190/fsr.v33i3.4050',
    ),
    3 => 
    array (
      'judul' => 'Financial literacy and household financial resilience',
      'penulis' => 'Taixing Liu dkk.',
      'jurnal' => 'Finance Research Letters',
      'url' => 'https://doi.org/10.1016/j.frl.2024.105378',
    ),
    4 => 
    array (
      'judul' => 'Precautionary Savings Against Health Risks',
      'penulis' => 'Tansel Yilmazer, Robert L. Scharff',
      'jurnal' => 'Research on Aging',
      'url' => 'https://doi.org/10.1177/0164027512473487',
    ),
    5 => 
    array (
      'judul' => 'The role and relevance of domain knowledge, perceptions of planning importance, and risk tolerance in predicting savings intentions',
      'penulis' => 'Gerry Croy dkk.',
      'jurnal' => 'Journal of Economic Psychology',
      'url' => 'https://doi.org/10.1016/j.joep.2010.06.002',
    ),
    6 => 
    array (
      'judul' => 'The Effect of Student Loan Debt on Emergency Savings and the Moderating Role of Financial Knowledge: Evidence from the U.S. Survey of Household Economics and Decisionmaking',
      'penulis' => 'Thomas Korankye dkk.',
      'jurnal' => 'Journal of Risk and Financial Management',
      'url' => 'https://doi.org/10.3390/jrfm17090420',
    ),
    7 => 
    array (
      'judul' => 'Financial Wellbeing and Financial Resilience: Insights from Personal Experiences and Gender Differences',
      'penulis' => 'Arturo Garcia-Santillan dkk.',
      'jurnal' => 'Journal of Risk and Financial Management',
      'url' => 'https://doi.org/10.3390/jrfm19030217',
    ),
    8 => 
    array (
      'judul' => 'The Interplay of Mental Budgeting, Self-Control, and Financial Behavior: Implications for Individual Financial Well-Being',
      'penulis' => 'Syed Atif Ali dkk.',
      'jurnal' => 'Pakistan Journal of Humanities and Social Sciences',
      'url' => 'https://doi.org/10.52131/pjhss.2024.v12i2.2102',
    ),
    9 => 
    array (
      'judul' => 'Digital financial literacy and savings behavior: A comprehensive cross-country analysis of FinTech adoption patterns and economic outcomes across 12 nations',
      'penulis' => 'Doğan Başar dkk.',
      'jurnal' => 'Borsa Istanbul Review',
      'url' => 'https://doi.org/10.1016/j.bir.2025.09.004',
    ),
  ),
  'Pinjaman Online, Bunga, dan Risiko Utang' => 
  array (
    0 => 
    array (
      'judul' => 'The Effect of Student Loan Debt on Emergency Savings and the Moderating Role of Financial Knowledge: Evidence from the U.S. Survey of Household Economics and Decisionmaking',
      'penulis' => 'Thomas Korankye dkk.',
      'jurnal' => 'Journal of Risk and Financial Management',
      'url' => 'https://doi.org/10.3390/jrfm17090420',
    ),
    1 => 
    array (
      'judul' => 'The influence of the buy-now-pay-later payment mode on consumer spending decisions',
      'penulis' => 'Rhys Ashby dkk.',
      'jurnal' => 'Journal of Retailing',
      'url' => 'https://doi.org/10.1016/j.jretai.2025.01.003',
    ),
    2 => 
    array (
      'judul' => 'The Interplay of Mindfulness, Impulsive Buying, and Perceived Risk in Buy Now Pay Later (BNPL) Usage among Mature Generations in Indonesia',
      'penulis' => 'Isnaini Nuzula Agustin dkk.',
      'jurnal' => 'Journal of Consumer Sciences',
      'url' => 'https://doi.org/10.29244/jcs.10.3.535-553',
    ),
    3 => 
    array (
      'judul' => 'Buy now, pay later consumer credit behavior: impacts on financing decisions',
      'penulis' => 'Enrico Maria Cervellati dkk.',
      'jurnal' => 'Qualitative Research in Financial Markets',
      'url' => 'https://doi.org/10.1108/QRFM-07-2024-0185',
    ),
    4 => 
    array (
      'judul' => 'The economics of “Buy Now, Pay Later”: A merchant’s perspective',
      'penulis' => 'Tobias Berg dkk.',
      'jurnal' => 'Journal of Financial Economics',
      'url' => 'https://doi.org/10.1016/j.jfineco.2025.104093',
    ),
    5 => 
    array (
      'judul' => 'The effects of buy now, pay later (BNPL) on customers’ online purchase behavior',
      'penulis' => 'Ashish Kumar dkk.',
      'jurnal' => 'Journal of Retailing',
      'url' => 'https://doi.org/10.1016/j.jretai.2024.09.004',
    ),
    6 => 
    array (
      'judul' => 'Buy Now, Pay Later Loans, Social Norms, and Consumer Indebtedness',
      'penulis' => 'Lucy F. Ackert dkk.',
      'jurnal' => 'Journal of Behavioral Finance',
      'url' => 'https://doi.org/10.1080/15427560.2024.2385898',
    ),
    7 => 
    array (
      'judul' => 'Buy Now Pay Later—A Fad or a Reality? A Perspective on Electronic Commerce',
      'penulis' => 'Dana Adriana Lupșa-Tătaru dkk.',
      'jurnal' => 'Economies',
      'url' => 'https://doi.org/10.3390/economies11080218',
    ),
    8 => 
    array (
      'judul' => 'Research on Psychological Motivation of Buy-now-pay-later in Consumer Finance: Benefits and Risks for Consumer',
      'penulis' => 'Danxuan Zhu',
      'jurnal' => 'Scientific Journal of Economics and Management Research',
      'url' => 'https://doi.org/10.54691/sab44988',
    ),
    9 => 
    array (
      'judul' => 'Self-control, financial literacy and consumer over-indebtedness',
      'penulis' => 'John Gathergood',
      'jurnal' => 'Journal of Economic Psychology',
      'url' => 'https://doi.org/10.1016/j.joep.2011.11.006',
    ),
  ),
  'Literasi Digital dan Jejak Finansial' => 
  array (
    0 => 
    array (
      'judul' => 'Digital literacy, online security behaviors and E-payment intention',
      'penulis' => 'Thu Thuy Nguyen dkk.',
      'jurnal' => 'Journal of Open Innovation: Technology, Market, and Complexity',
      'url' => 'https://doi.org/10.1016/j.joitmc.2024.100292',
    ),
    1 => 
    array (
      'judul' => 'Digital financial literacy and financial well-being',
      'penulis' => 'Youngjoo Choung dkk.',
      'jurnal' => 'Finance Research Letters',
      'url' => 'https://doi.org/10.1016/j.frl.2023.104438',
    ),
    2 => 
    array (
      'judul' => 'Digital financial literacy and savings behavior: A comprehensive cross-country analysis of FinTech adoption patterns and economic outcomes across 12 nations',
      'penulis' => 'Doğan Başar dkk.',
      'jurnal' => 'Borsa Istanbul Review',
      'url' => 'https://doi.org/10.1016/j.bir.2025.09.004',
    ),
    3 => 
    array (
      'judul' => 'Examining the Impact of Financial Literacy, Financial Self-Control, and Demographic Determinants on Individual Financial Performance and Behavior: An Insight from the Lebanese Crisis Period',
      'penulis' => 'Jeanne Laure Mawad dkk.',
      'jurnal' => 'Sustainability',
      'url' => 'https://doi.org/10.3390/su142215129',
    ),
    4 => 
    array (
      'judul' => 'Does Financial Literacy Affect Household Financial Behavior? The Role of Limited Attention',
      'penulis' => 'Shulin Xu dkk.',
      'jurnal' => 'Frontiers in Psychology',
      'url' => 'https://doi.org/10.3389/fpsyg.2022.906153',
    ),
    5 => 
    array (
      'judul' => 'On the Rise of FinTechs: Credit Scoring Using Digital Footprints',
      'penulis' => 'Tobias Berg dkk.',
      'jurnal' => 'The Review of Financial Studies',
      'url' => 'https://doi.org/10.1093/rfs/hhz099',
    ),
    6 => 
    array (
      'judul' => 'Analysis of Factors that Influence Customers’ Willingness to Leave Big Data Digital Footprints on Social Media: A Systematic Review of Literature',
      'penulis' => 'Syed Sardar Muhammad dkk.',
      'jurnal' => 'Information Systems Frontiers',
      'url' => 'https://doi.org/10.1007/s10796-017-9802-y',
    ),
    7 => 
    array (
      'judul' => 'Understanding Students’ Consumption Behavior: The Role of Digital Payment, Financial Literacy, and Lifestyle',
      'penulis' => 'Atikah Septiani dkk.',
      'jurnal' => 'Talent: Journal of Economics and Business',
      'url' => 'https://doi.org/10.59422/jeb.v3i03.973',
    ),
    8 => 
    array (
      'judul' => 'Influence of Perceived Usefulness, Trust, Perceived Ease of Use and Social Influence on E-Wallet Adoption in Generation Z',
      'penulis' => 'Melva Hermayanty Saragih, Marco Christian Mulyadi',
      'jurnal' => 'Pedagogic Research-Applied Literacy Journal',
      'url' => 'https://doi.org/10.70574/stfrsq53',
    ),
    9 => 
    array (
      'judul' => 'Effect of Consumer Trust and Perceived Risk on e-Wallet Adoption: Consideration for Technology Startup Entrepreneurs',
      'penulis' => 'Melisa Krisnawati dkk.',
      'jurnal' => 'Jurnal Entrepreneur dan Entrepreneurship',
      'url' => 'https://doi.org/10.37715/jee.v10i2.2212',
    ),
  ),
  'Diversifikasi dan Manajemen Risiko' => 
  array (
    0 => 
    array (
      'judul' => 'Financial literacy and international portfolio diversification',
      'penulis' => 'Simona Barone dkk.',
      'jurnal' => 'International Review of Economics &amp; Finance',
      'url' => 'https://doi.org/10.1016/j.iref.2025.104876',
    ),
    1 => 
    array (
      'judul' => 'The Effect of Financial Literacy on Portfolio Diversification',
      'penulis' => 'Hiroyuki Miyamoto, Yoko Nishide',
      'jurnal' => 'Journal of Behavioral Economics and Finance',
      'url' => 'https://doi.org/10.11167/jbef.17.42',
    ),
    2 => 
    array (
      'judul' => 'Financial literacy and portfolio diversification',
      'penulis' => 'Margarida Abreu, Victor Mendes',
      'jurnal' => 'Quantitative Finance',
      'url' => 'https://doi.org/10.1080/14697680902878105',
    ),
    3 => 
    array (
      'judul' => 'Financial Literacy and Portfolio Diversity in China',
      'penulis' => 'Congmin Peng dkk.',
      'jurnal' => 'Journal of Family and Economic Issues',
      'url' => 'https://doi.org/10.1007/s10834-021-09810-3',
    ),
    4 => 
    array (
      'judul' => 'Financial literacy and portfolio diversification: an observation from the Tunisian stock market',
      'penulis' => 'Amari Mouna, Anis Jarboui',
      'jurnal' => 'International Journal of Bank Marketing',
      'url' => 'https://doi.org/10.1108/IJBM-03-2015-0032',
    ),
    5 => 
    array (
      'judul' => 'Is portfolio diversification still effective: evidence spanning three crises from the perspective of U.S. investors',
      'penulis' => 'Rong Huang dkk.',
      'jurnal' => 'Journal of Asset Management',
      'url' => 'https://doi.org/10.1057/s41260-025-00398-z',
    ),
    6 => 
    array (
      'judul' => 'The benefits of sectoral diversification for investors with different risk perceptions',
      'penulis' => 'Serdar Yaman, Mert Baran Tuncel',
      'jurnal' => 'Borsa Istanbul Review',
      'url' => 'https://doi.org/10.1016/j.bir.2025.02.008',
    ),
    7 => 
    array (
      'judul' => 'Financial Literacy and Financial Risk Tolerance of Individual Investors: Multinomial Logistic Regression Approach',
      'penulis' => 'Yılmaz Bayar dkk.',
      'jurnal' => 'Sage Open',
      'url' => 'https://doi.org/10.1177/2158244020945717',
    ),
    8 => 
    array (
      'judul' => 'The Impact of Financial Literacy, Generation, and Socioeconomic Factors on Financial Risk Tolerance: An African American Study',
      'penulis' => 'John H. Young',
      'jurnal' => 'The Review of Black Political Economy',
      'url' => 'https://doi.org/10.1177/00346446231152805',
    ),
    9 => 
    array (
      'judul' => 'Effects of diversification of assets in optimizing risk of portfolio',
      'penulis' => 'Dare Jayeola dkk.',
      'jurnal' => 'Malaysian Journal of Fundamental and Applied Sciences',
      'url' => 'https://doi.org/10.11113/mjfas.v0n0.567',
    ),
  ),
  'Mengenali Penipuan Finansial Online' => 
  array (
    0 => 
    array (
      'judul' => 'The impact of time pressure and type of fraud on susceptibility to online fraud',
      'penulis' => 'Ce Lyu dkk.',
      'jurnal' => 'Frontiers in Psychology',
      'url' => 'https://doi.org/10.3389/fpsyg.2025.1508363',
    ),
    1 => 
    array (
      'judul' => 'Fishing for phishy messages: predicting phishing susceptibility through the lens of cyber-routine activities theory and heuristic-systematic model',
      'penulis' => 'Chin Lay Gan dkk.',
      'jurnal' => 'Humanities and Social Sciences Communications',
      'url' => 'https://doi.org/10.1057/s41599-024-04083-1',
    ),
    2 => 
    array (
      'judul' => 'Friend or phisher: how known senders and fear of missing out affect young adults\' phishing susceptibility on social media',
      'penulis' => 'Jennifer Klütsch dkk.',
      'jurnal' => 'Humanities and Social Sciences Communications',
      'url' => 'https://doi.org/10.1057/s41599-024-03412-8',
    ),
    3 => 
    array (
      'judul' => 'Analytical reasoning reduces internet fraud susceptibility',
      'penulis' => 'Nicholas J. Kelley dkk.',
      'jurnal' => 'Computers in Human Behavior',
      'url' => 'https://doi.org/10.1016/j.chb.2022.107648',
    ),
    4 => 
    array (
      'judul' => 'Financial literacy and fraud vulnerability in digital finance: Evidence from the 2024 NFCS',
      'penulis' => 'Denada Ibrushi dkk.',
      'jurnal' => 'Finance Research Letters',
      'url' => 'https://doi.org/10.1016/j.frl.2026.109674',
    ),
    5 => 
    array (
      'judul' => 'The Consumer Scam: An Agency-Theoretic Approach',
      'penulis' => 'Sareh Pouryousefi, Jeff Frooman',
      'jurnal' => 'Journal of Business Ethics',
      'url' => 'https://doi.org/10.1007/s10551-017-3466-x',
    ),
    6 => 
    array (
      'judul' => 'Research Article Phishing Susceptibility: An Investigation Into the Processing of a Targeted Spear Phishing Email',
      'penulis' => 'Jingguo Wang dkk.',
      'jurnal' => 'IEEE Transactions on Professional Communication',
      'url' => 'https://doi.org/10.1109/TPC.2012.2208392',
    ),
    7 => 
    array (
      'judul' => 'Which factors predict susceptibility to phishing? An empirical study',
      'penulis' => 'Liliana Ribeiro dkk.',
      'jurnal' => 'Computers &amp; Security',
      'url' => 'https://doi.org/10.1016/j.cose.2023.103558',
    ),
    8 => 
    array (
      'judul' => 'Susceptibility to phishing on social network sites: A personality information processing model',
      'penulis' => 'Edwin Donald Frauenstein, Stephen Flowerday',
      'jurnal' => 'Computers &amp; Security',
      'url' => 'https://doi.org/10.1016/j.cose.2020.101862',
    ),
    9 => 
    array (
      'judul' => 'Predicting susceptibility to social influence in phishing emails',
      'penulis' => 'Kathryn Parsons dkk.',
      'jurnal' => 'International Journal of Human-Computer Studies',
      'url' => 'https://doi.org/10.1016/j.ijhcs.2019.02.007',
    ),
  ),
);
    }
}
