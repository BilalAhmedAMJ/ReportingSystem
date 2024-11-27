###Document Access Rules

1.  All Users: Used for "pulic" documents
    ```{
        rule_id:1,label:"All Users", 
        upload_allowed:["sys-admin","admin","national-general-secretary"],
        download_allowed:["office-bearer"]
        user_type:[],branch_id:[]
    }```

2. Local Secretary: Used for distribution of documents down the hirarchy, or horizontally
    ```{
        rule_id:2,label:"Local Secretary", 
        upload_allowed:["office_bearer"],
        download_allowed:["office-bearer"],
        user_type:"select_all_by_default_pick_from_uploading_users_depts",
        branch_id:"pick_from_uploading_users_depts",
        access_rules:{'branch_children':<false,true_if_none_seletec>,'branch_parents':true}
    }```
3. Selected Department
    ```{
        rule_id:3,label:"Selected Secretary", 
        upload_allowed:["sys-admin","admin","national-general-secretary","admin-upload-documents"],
        download_allowed:["office-bearer"],
        user_type:"pick_from_uploading_users_depts",
        branch_id:"pick_from_uploading_users_depts",
        access_rules:{'branch_children':<false,true_if_none_seletec>,'branch_parents':true}
    }```
4. Local Mal Team
    ```{
        rule_id:4,label:"Local Mal Team", 
        upload_allowed:["sys-admin","national-general-secretary","mal-report-upload"],
        download_allowed:["office-bearer"],
        user_type:"auto_select_mal_related_depts",
        branch_id:"force_selection_of_branch",
        access_rules:{'branch_children':false,'branch_parents':true}
    }```

5. Department of National GS
    ```{
        rule_id:5,label:"Department of National GS", 
        upload_allowed:["sys-admin","national-general-secretary","mal-report-upload"],
        download_allowed:["office-bearer"],
        user_type:"mal_related_depts",
        branch_id:"national_markaz",
        access_rules:{'branch_children':false,'branch_parents':true}
    }```

5. Imarat / National Secretary
    ```{
        rule_id:5,label:"Imarat / National Counterpart", 
        upload_allowed:["office-bearer"],
        download_allowed:["office-bearer"],
        user_type:"mal_related_depts",
        branch_id:"force_selection_of_branch",
        access_rules:{'branch_children':false,'branch_parents':true}
    }```
