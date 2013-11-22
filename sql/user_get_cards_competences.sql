SET ANSI_NULLS ON
SET QUOTED_IDENTIFIER ON
GO

CREATE  procedure dbo.user_get_cards_competences 
	@person_id int , @period int

as

declare @cardID int
declare @subordinates int

SELECT @cardID = id 
FROM user_rp_ach_cards
WHERE (person_id = @person_id) AND (period = @period)

-- фиксируем наличие подчинённых
SELECT @subordinates = CASE WHEN P.pid IS NULL THEN 0 ELSE 1 END
FROM user_rp_tree_posts P, user_rp_tree_posts_employees_PM PE
WHERE (PE.person_id = @person_id) AND (P.id = PE.post_id)


-- Если период действия карточки меньше 2013г, старый алгоритм с компетенциями
IF @period < 2013
BEGIN
  -- Получаем список основных компетенций по person_id  и period
  select distinct user_rp_ach_cards.id as card_id, G.group_id , competence_id, C.additional
  from user_rp_employees_attribs A, user_rp_ach_cards, user_rp_ach_groups_employees G

      inner join user_rp_ach_groups_employees_competences GP
          on G.group_id=GP.group_id

      inner join user_rp_ach_competences C
          on GP.competence_id=C.id

              where user_rp_ach_cards.id=@cardID and
                  A.person_id=user_rp_ach_cards.person_id 
                  and (A.grade between grade_min and grade_max or (grade_min is null and grade_max is null))
                  and (A.business_unit_id=G.business_unit_id or G.business_unit_id is null)
                  and (A.job_family_id=G.job_family_id or G.job_family_id is null)
                  --and (A.appointment_id=G.appointment_id or G.appointment_id is null)
                  --and G.disabled=0
                  -- связь по типу персонала - только для корпоратвных компетенций
                  and (C.additional=0 and G.type=A.type) 

  -- Получаем список дополнительных компетенций по person_id  и period
  select distinct user_rp_ach_cards.id as card_id, G.group_id , competence_id, C.additional
  from user_rp_employees_attribs A,user_rp_ach_cards, user_rp_ach_groups_employees G

      inner join user_rp_ach_groups_employees_competences GP
          on G.group_id=GP.group_id

      inner join user_rp_ach_competences C
          on GP.competence_id=C.id

              where user_rp_ach_cards.id=@cardID and period<>2006
                  and A.person_id=user_rp_ach_cards.person_id 
                  and (A.grade between grade_min and grade_max or (grade_min is null and grade_max is null))
                  and (A.business_unit_id=G.business_unit_id or G.business_unit_id is null)
                  and (A.job_family_id=G.job_family_id or G.job_family_id is null)
                  --and (A.appointment_id=G.appointment_id or G.appointment_id is null)
                  --and G.disabled=0
                  -- связь по типу персонала - только для дополнительных компетенций
                  and C.additional=1
END
-- Иначе - новый алгоритм
ELSE
BEGIN
	-- Получаем список компетенций по person_id  и period
  select distinct C.id as card_id, CO.id as competence_id
  from user_rp_employees_attribs A, user_rp_ach_cards C, user_rp_ach_competences CO

	where C.id=@cardID 
    	and  A.person_id = C.person_id 
        and (A.grade between CO.grade_from and CO.grade_to)
        and CO.has_subordinates = 0
        
	IF @subordinates = 1
    BEGIN
        select distinct C.id as card_id, CO.id as competence_id
        from user_rp_employees_attribs A, user_rp_ach_cards C, user_rp_ach_competences CO

            where C.id=@cardID 
                and  A.person_id = C.person_id 
                and (A.grade between CO.grade_from and CO.grade_to)
                and CO.has_subordinates = 1
    END
END