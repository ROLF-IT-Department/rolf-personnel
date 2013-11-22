USE [lite]
GO
/****** Object:  StoredProcedure [dbo].[user_proc_rp_ach_insert_cards_competences]    Script Date: 11/22/2013 13:12:28 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
/**
 * Обновление компетенций сотрдника
 * 
 * @last_update 15.08.2011 by SMGladkovskiy@rolf.ru
 */
ALTER  procedure [dbo].[user_proc_rp_ach_insert_cards_competences] @card_id int

as

declare @Actual_competences table (id int, competence_id int)
declare @card_competences table (card_id int, competence_id int)
declare @new_person_id int
declare @person_id int
declare @cardPeriod smallint
declare @subordinates int

-- проверка на наличие переходов на новую должность.
-- так как берётся person_id из карточки, то это самый первый person_id в системе.
-- необходимо искать актуальный и по нему смотреть компетенции
-- Обозначаем период
SELECT @new_person_id = MAX(I.ref_id), @cardPeriod = MAX(Ca.period), @person_id = MAX(Ca.person_id)
FROM 
	user_rp_persons_integrated_sap I,
	user_rp_ach_cards Ca
WHERE 
	Ca.id = @card_id
	AND I.person_id = Ca.person_id 
    
GROUP BY I.person_id;

SELECT @subordinates = CASE WHEN P.pid IS NULL THEN 0 ELSE 1 END
	FROM user_rp_tree_posts P, user_rp_tree_posts_employees_PM PE
	WHERE (PE.person_id = @person_id) AND (P.id = PE.post_id)
	
	

IF @cardPeriod < 2013
BEGIN
    -- вставка доп. компетенций - за 2006-й год их не было
    insert into @Actual_competences (id,competence_id)
    select distinct
        Ca.id,
        competence_id
    from 
        user_rp_employees_attribs A,
        user_rp_ach_cards Ca,
        user_rp_ach_groups_employees G
    inner join user_rp_ach_groups_employees_competences GP
        on G.group_id = GP.group_id
    inner join user_rp_ach_competences C
        on GP.competence_id = C.id
    where 
        Ca.id = @card_id
        and period <> 2006
        and A.person_id = CASE WHEN isnull(@new_person_id, 0) = 0 THEN Ca.person_id ELSE @new_person_id END 
        and (A.grade between grade_min and grade_max or (grade_min is null and grade_max is null))
        and (A.business_unit_id = G.business_unit_id or G.business_unit_id is null)
        and (A.job_family_id = G.job_family_id or G.job_family_id is null)
        --and (A.appointment_id=G.appointment_id or G.appointment_id is null)
        --and G.disabled=0
        and C.additional = 1

    -- вставка основных компетенций
    insert into @Actual_competences (id,competence_id)
    select distinct 
        Ca.id,
        competence_id
    from 
        user_rp_employees_attribs A,
        user_rp_ach_cards Ca,
        user_rp_ach_groups_employees G
    inner join user_rp_ach_groups_employees_competences GP
        on G.group_id = GP.group_id
    inner join user_rp_ach_competences C
        on GP.competence_id = C.id
    where
        Ca.id = @card_id
        and A.person_id = CASE WHEN isnull(@new_person_id, 0) = 0 THEN Ca.person_id ELSE @new_person_id END 
        and (A.grade between grade_min and grade_max or (grade_min is null and grade_max is null))
        and (A.business_unit_id = G.business_unit_id or G.business_unit_id is null)
        and (A.job_family_id = G.job_family_id or G.job_family_id is null)
        --and (A.appointment_id=G.appointment_id or G.appointment_id is null)
        --and G.disabled=0
        and (C.additional = 0 and G.type=A.type) 
END
ELSE
BEGIN
	 -- вставка основных компетенций
--    insert into @Actual_competences (id,competence_id) 
--    EXEC user_get_cards_competences @personId, @cardPeriod
    
    	-- Получаем список компетенций по person_id  и period
  insert into @card_competences (card_id,competence_id)
  select distinct C.id as card_id, CO.id as competence_id
  from user_rp_employees_attribs A, user_rp_ach_cards C, user_rp_ach_competences CO

	where C.id=@card_id
    	and  A.person_id = C.person_id 
        and (A.grade between CO.grade_from and CO.grade_to)
        and CO.has_subordinates = 0
        
	IF @subordinates = 1
    BEGIN
		insert into @card_competences (card_id,competence_id)
        select distinct C.id as card_id, CO.id as competence_id
        from user_rp_employees_attribs A, user_rp_ach_cards C, user_rp_ach_competences CO

            where C.id=@card_id
                and  A.person_id = C.person_id 
                and (A.grade between CO.grade_from and CO.grade_to)
                and CO.has_subordinates = 1
    END
    
    insert into @Actual_competences (id,competence_id) 
    select card_competences.* 
	from 
    (select * from @card_competences) card_competences
    
END;
        
        


-- вставить недостающие компетенции
insert into user_rp_ach_cards_competences (card_id, competence_id)
select Actual_competences.* 
from 
    (select * from @Actual_competences where id = @card_id) Actual_competences
full join
    (select * from user_rp_ach_cards_competences where card_id = @card_id and competence_id is not null) Current_competences
        on Actual_competences.id = Current_competences.card_id 
           and Actual_competences.competence_id = Current_competences.competence_id
where Current_competences.competence_id is null

 -- пометить как архивные, компетенции которых нет в актуальных 
update user_rp_ach_cards_competences
set disabled = 1
where
    card_id = @card_id
    and competence_id in
    (
        select Current_competences.competence_id 
        from (select * from @Actual_competences where id = @card_id) Actual_competences
        full join 
            (select * from user_rp_ach_cards_competences where card_id = @card_id and competence_id is not null) Current_competences	
                on
                    Actual_competences.id = Current_competences.card_id 
                    and Actual_competences.competence_id = Current_competences.competence_id
        where Actual_competences.competence_id is null
    )
