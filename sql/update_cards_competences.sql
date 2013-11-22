DECLARE @card_id int
	
DECLARE c1 CURSOR READ_ONLY
FOR
SELECT id
FROM dbo.user_rp_ach_cards
WHERE period IN (2013,2014)

OPEN c1

FETCH NEXT FROM c1
INTO @card_id

WHILE @@FETCH_STATUS = 0
BEGIN

	EXEC user_proc_rp_ach_insert_cards_competences @card_id

	FETCH NEXT FROM c1
	INTO @card_id

END

CLOSE c1
DEALLOCATE c1