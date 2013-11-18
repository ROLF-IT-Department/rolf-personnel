--------------- SQL ---------------

ALTER TABLE [dbo].[user_rp_ach_competences]
ADD [grade_from] tinyint NULL
GO

EXEC sp_addextendedproperty 'MS_Description', N'Начальное значение грейда для данной компетенции', N'schema', N'dbo', N'table', N'user_rp_ach_competences', N'column', N'grade_from'
GO

--------------- SQL ---------------

ALTER TABLE [dbo].[user_rp_ach_competences]
ADD [grade_to] tinyint NULL
GO

EXEC sp_addextendedproperty 'MS_Description', N'Конечное значение грейда для компетенции', N'schema', N'dbo', N'table', N'user_rp_ach_competences', N'column', N'grade_to'
GO

--------------- SQL ---------------

ALTER TABLE [dbo].[user_rp_ach_competences]
ADD [has_subordinates] tinyint DEFAULT 0 WITH VALUES NOT NULL
GO

EXEC sp_addextendedproperty 'MS_Description', N'Флаг наличия подчинённых', N'schema', N'dbo', N'table', N'user_rp_ach_competences', N'column', N'has_subordinates'
GO

--------------- SQL ---------------

/* Data for the 'dbo.user_rp_ach_competences' table  (Records 32 - 39) */

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (32, N'Энергичность. 
Способность вдохновлять.<br />
<em>Energize.<br />
The ability to energize others</em>', N'Мотивировать и убеждать сотрудников, вдохновлять на совершение невозможного, инициировать новое.<br />
<em>Positive energy is the ability to get other people revved up. People who energize can inspire their team to take on the impossible</em>', N'- Постановка понятных целей;
- Уверенность в себе как в лидере;
- Эффективная командная работа;
- Правильный подбор рабочей группы,  Идентифицируемость и  оцениваемость личного вклада каждого;
- Мотивация сотрудников, использование силы убеждения.', N'- Set clear goals.
- Be a confident leader.
- Effective teamwork.
- Team members are to be placed precisely.
- Individual contribution is identified and evaluated.
- Staff motivation and strong persuasion skills.', NULL, NULL, NULL, 1, 7, 0)
GO

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (33, N'Способность принимать решения.<br>
<em>Edge. The courage to make tough yes-or-no decisions.</em>', N'Принимать решения даже в условиях неопределенности, не изменять своему мнению.<br/>
<em>Effective leaders know when to stop assessing and make a tough decision, even without total information.</em>', N'- Развитые аналитические способности;
- Стратегическое мышление;
- Учет всех необходимых источников информации;
- Принятие решений с учетом стратегических позиций бизнеса; 
- Применение нужных ресурсов и информации;
- Способность решительно действовать в кризисных ситуациях.', N'- Developed analytical skills.
- Strategic thinking.
- All necessary sources of information are considered.
- Make decisions from strategic position.
- Application of right resources and information.
- Ability to act effectively in crisis situations', NULL, NULL, NULL, 1, 7, 0)
GO

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (34, N'Исполнительность<br>
<em>Execute. The ability to get the job done</em>', N'Знать как претворить решения в жизнь, не взирая на сопротивление и препятствия.<br />
<em>Leaders know how to put decisions into action and push them forward to completion, through resistance, chaos, or unexpected obstacles.</em>', N'- Способность руководителя правильно выбрать исполнителей;
- Умение четко ставить цели; 
- Использование различных способов контроля в зависимости от специфики поставленных задач', N'- Being able to appoint right people for execution.
- Ability to set objectives.
- Various forms of control depending on specific aims', NULL, NULL, NULL, 1, 7, 0)
GO

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (35, N'Экспертиза<br />
<em>Expertise</em>', N'Набор профессиональных знаний, умений и навыков, необходимых для успешной деятельности на своем рабочем месте<br />
<em>Professional competence expresses a set of qualification preconditions (usually called knowledge, skills and attitudes) necessary for a successful professional performance</em>', N'- Приобретение теоретических знаний;
- Усвоение профессионального опыта;
- Применение лучших методов работы;
- Передача профессиональных умений и навыков.', N'- Theoretical knowledge and professional experience. 
- The best  methods application in every day work.
- Skills and practices sharing', NULL, NULL, NULL, 8, 14, 0)
GO

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (36, N'Сотрудничество<br />
<em>Cooperation</em>', N'Умение трудиться совместно, стремление поддерживать целенаправленные рабочие отношения с коллегами на разных уровнях внутри организации<br />
<em>Skills and willingness to work in different collaborative networks as well as the ability to create meaningful working relationships within organization</em>', N'- Поддержание рабочих отношений;
- Интерес к мнению коллег, их вовлечение в процесс решения проблем;
- Поощрение атмосферы сотрудничества и взаимовыручки;
- Выявление интересов и потребностей коллег и клиентов с целью их удовлетворения', N'- Stromg working relations development and  support.
- Respect of colleagues opinion, involvement to problem solving processes.
- Creating the atmoshpere of cooperation and assistance.
- Colleagues and clients orientation', NULL, NULL, NULL, 8, 14, 0)
GO

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (37, N'Коммуникация<br />
<em>Communication</em>', N'Изложение своих мыслей ясно и структурировано. Предоставление информации в нужном объеме и форме. Владение тактикой ведения переговоров, аргументация и убеждение.<br />
<em>Communication competency is the ability to reach common goals through appropriate interaction. In order to achieve communication competency, staff must meet criteria: flexibility, involvement,  effectiveness.</em>', N'- Ясная передача информации;
- Использование специализированных терминов в соответствующих ситуациях;
- Умение слушать и слышать, формулировать вопросы;
- Умение давать обратную связь, отстаивать свою точку зрения', N'- Clear sharing the thought and ideas.
- Professional terms are used in proper situations.
- Skills to hear and listen, questions setting.
- Feedbacks are given in effective way.
- Ability to defend own point of view.', NULL, NULL, NULL, 8, 14, 0)
GO

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (38, N'Решение проблем<br />
<em>Problem solving</em>', N'Логичная постановка целей и активное стремление к их достижению. Проявление настойчивости при появлении препятствий и поиск различных путей для достижения результата.<br />
<em>A logical approach to address problems or manage the situation by drawing on colleagues’ knowledge and experience base, and calling on other references and resources as necessary.</em>', N'- Выделение составных частей проблемы и предложение правильных выводов;
- Перевод проблем в задачу;
- Определение исполнителей, сроков выполнения и меток контроля;
- Решение задачи;
- Запуск решения.', N'- Problems are divided into parts, the right conclusions are done.
- Problems are transferred into tasks.
- Set executives, deadlines and milestones. 
- Problem solving.
- Decisions are put into actions.', NULL, NULL, NULL, 8, 14, 0)
GO

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (39, N'Управление процессами<br />
<em>Process management</em>', N'При принятии решений учитывает стратегию компании, руководствуется коммерческой целесообразностью, оценивает ситуацию с точки зрения затрат и прибыльности.<br />
<em>Establishes a systematic course of action for self or others to ensure accomplishment of a specific objective. Sets priorities and goals to achieve maximum productivity.</em>', N'- Оценка эффективности каждого из вариантов решения;
- Нахождение путей увеличения производительности работы своего подразделения;
- Оценка всех действий с точки зрения возможностей компании на рынке (повышение прибыльности, перспектив роста и развития и пр.).', N'- Every decision effectiveness evaluation.
- Find ways to icrease the productivity of the business unit.
- All actions are estimated from the point of view of company''s market place (profit increase, growth and development opportunities etc).', NULL, NULL, NULL, 8, 14, 1)
GO

INSERT INTO [dbo].[user_rp_ach_competences] ([id], [name], [target], [description], [english_description], [additional], [disabled], [sort], [grade_from], [grade_to], [has_subordinates])
VALUES (40, N'Управление людьми<br />
<em>People management</em>', N'Постановка задач перед подчиненными; Распределение и делегирование работ; Контроль достижения поставленных целей<br />
<em>Willingly cooperates and works collaboratively toward solutions that generally benefit all involved parties; works cooperatively with others to accomplish company objectives.</em>', N'- Постановка конкретных задач и расстановка приоритетов;
- Согласование критериев оценки успешности работы;
- Распределение оптимальным образом  нагрузки среди подчиненных;
- Делегирование и координация;
- Согласование с подчиненными сроков для оценки результатов.', N'- Set precise objectives and priorities and criteria of successful job fulfillment.
- Delegate evenly.
- Coordination.
- Subordinates are informed about deadlines and evaluation process.', NULL, NULL, NULL, 8, 14, 1)
GO